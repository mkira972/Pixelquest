<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

/**
 * LE PANIER, STOCKE DANS UN COOKIE.
 *
 * Le cahier des charges demande une "gestion de cookies" : c'est ici.
 *
 * Pourquoi un cookie et pas la base de donnees ? Parce que je veux qu'un
 * visiteur pas encore connecte puisse remplir son panier. Si je le stockais
 * en base, il me faudrait deja un compte pour savoir a qui il appartient.
 * La, le panier vit dans le navigateur du visiteur, et il le retrouve
 * intact meme apres s'etre connecte ou inscrit.
 *
 * Le contenu du cookie est un JSON tout simple :
 *   {"3": 2, "7": 1}   = le jeu n3 en 2 exemplaires, le jeu n7 en 1
 *
 * Je ne stocke que des ids et des quantites, jamais les prix. Les prix
 * sont toujours relus depuis la base, sinon n'importe qui pourrait
 * modifier son cookie pour payer moins cher.
 */
class CartController extends Controller
{
    public const COOKIE_NAME = 'panier';
    public const COOKIE_DUREE = 43200; // 30 jours, exprimes en minutes

    /**
     * Lit le panier depuis le cookie de la requete.
     *
     * Elle est static parce que OrderController en a besoin aussi au
     * moment de valider la commande : comme ca il n'a pas a instancier
     * tout le controleur du panier.
     */
    public static function panier(Request $request): array
    {
        // $request->cookie() dechiffre automatiquement (voir EncryptCookies)
        $raw = $request->cookie(self::COOKIE_NAME);
        $data = json_decode($raw ?? '[]', true);

        if (! is_array($data)) {
            return [];   // cookie absent ou illisible : panier vide
        }

        // NETTOYAGE. Le cookie vient du navigateur du visiteur, donc je ne
        // lui fais pas confiance, meme s'il est chiffre. Je force tout en
        // entier et je jette ce qui n'a pas de sens.
        $clean = [];
        foreach ($data as $id => $qty) {
            $id = (int) $id;
            $qty = (int) $qty;
            if ($id > 0 && $qty > 0) {
                $clean[$id] = min($qty, 99);   // plafond a 99 par jeu
            }
        }

        return $clean;
    }

    /**
     * Ecrit le panier dans le cookie.
     *
     * Cookie::queue() ne l'envoie pas tout de suite : il le met en attente,
     * et Laravel l'attache automatiquement a la reponse. C'est pour ca que
     * je peux appeler cette methode puis faire un back() juste apres.
     */
    protected function sauvegarder(array $panier): void
    {
        Cookie::queue(self::COOKIE_NAME, json_encode($panier), self::COOKIE_DUREE);
    }

    /**
     * Affiche la page du panier.
     */
    public function index(Request $request): View
    {
        $panier = self::panier($request);

        // Une seule requete pour tous les jeux du panier, au lieu d'une
        // par ligne. whereIn = "WHERE id IN (3, 7, 12)".
        $products = Product::whereIn('id', array_keys($panier))->get();

        // Le total est calcule ici, a partir des prix de la BASE.
        // fn() est une fonction flechee, la version courte de function().
        $total = $products->sum(fn ($p) => $p->price * ($panier[$p->id] ?? 0));

        return view('cart.index', compact('products', 'panier', 'total'));
    }

    /**
     * Ajoute un jeu au panier.
     *
     * Le parametre Product $product fait du "route model binding" :
     * la route est /panier/ajouter/{product}, et Laravel va chercher
     * tout seul le jeu correspondant en base. Si l'id n'existe pas,
     * il renvoie une 404 sans que j'aie rien a ecrire.
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:99']]);

        if ($product->stock < 1) {
            return back()->with('error', "« {$product->name} » est en rupture de stock.");
        }

        $panier = self::panier($request);
        $qty    = (int) $request->input('quantity', 1);

        // Si le jeu est deja dans le panier j'additionne, mais je ne
        // depasse jamais le stock disponible.
        $panier[$product->id] = min(($panier[$product->id] ?? 0) + $qty, $product->stock);

        $this->sauvegarder($panier);

        // with() met un message en session pour UNE seule requete.
        // Il est affiche par la vue partials/flash.blade.php puis disparait.
        return back()->with('success', "« {$product->name} » a ete ajoute au panier.");
    }

    /**
     * Change la quantite d'une ligne du panier.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);

        $panier = self::panier($request);
        $qty    = (int) $request->input('quantity');

        // Mettre 0 revient a retirer le jeu du panier
        if ($qty <= 0) {
            unset($panier[$product->id]);
        } else {
            $panier[$product->id] = min($qty, max($product->stock, 1));
        }

        $this->sauvegarder($panier);

        return redirect()->route('cart.index')->with('success', 'Panier mis a jour.');
    }

    /**
     * Retire un jeu du panier.
     */
    public function remove(Request $request, Product $product): RedirectResponse
    {
        $panier = self::panier($request);
        unset($panier[$product->id]);
        $this->sauvegarder($panier);

        return redirect()->route('cart.index')->with('success', 'Produit retire du panier.');
    }

    /**
     * Vide tout le panier.
     * Cookie::forget() fabrique un cookie deja expire, ce qui dit au
     * navigateur de le supprimer.
     */
    public function clear(): RedirectResponse
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));

        return redirect()->route('cart.index')->with('success', 'Panier vide.');
    }
}
