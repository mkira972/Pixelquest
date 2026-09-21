<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * LES COMMANDES COTE CLIENT.
 *
 * C'est ici que le panier (un cookie) devient une vraie commande
 * enregistree en base et rattachee au compte.
 *
 * Toutes les routes de ce controleur sont derriere le middleware 'auth',
 * donc $request->user() renvoie toujours quelqu'un, jamais null.
 */
class OrderController extends Controller
{
    /**
     * La page "Mes commandes".
     *
     * Je passe par $request->user()->orders() et pas par Order::all() :
     * comme ca un client ne peut voir que SES commandes, c'est filtre
     * a la source. with('products') charge les jeux en une seule requete
     * au lieu d'une par commande (ca evite le probleme du N+1).
     */
    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $request->user()->orders()->with('products')->paginate(10),
        ]);
    }

    /**
     * Le detail d'une commande.
     *
     * Le abort_unless est important : sans lui, n'importe qui pourrait
     * taper /mes-commandes/42 et lire la commande d'un autre client.
     * Etre connecte ne suffit pas, il faut que la commande soit bien
     * la sienne.
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('products');

        return view('orders.show', compact('order'));
    }

    /**
     * Le formulaire d'adresse avant de valider.
     * Si le panier est vide, inutile d'aller plus loin.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $panier   = CartController::panier($request);
        $products = Product::whereIn('id', array_keys($panier))->get();

        if ($products->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $total = $products->sum(fn ($p) => $p->price * ($panier[$p->id] ?? 0));

        return view('orders.checkout', compact('products', 'panier', 'total'));
    }

    /**
     * L'ENREGISTREMENT DE LA COMMANDE. C'est la methode la plus
     * importante du projet.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. VALIDATION. Si un champ ne passe pas, Laravel renvoie tout
        // seul vers le formulaire avec les messages d'erreur. La suite
        // du code n'est meme pas executee.
        $data = $request->validate([
            'shipping_address'  => ['required', 'string', 'max:255'],
            'shipping_city'     => ['required', 'string', 'max:100'],
            'shipping_zip_code' => ['required', 'string', 'max:20'],
        ]);

        $panier   = CartController::panier($request);
        $products = Product::whereIn('id', array_keys($panier))->get();

        if ($products->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // 2. LA TRANSACTION.
        // J'ecris dans trois tables : orders, order_product, et le stock
        // dans products. Si une seule de ces ecritures plante, la
        // transaction annule TOUT. Sans ca je pourrais me retrouver avec
        // une commande a moitie creee, ou un stock diminue alors qu'aucune
        // commande n'existe.
        $order = DB::transaction(function () use ($request, $products, $panier, $data) {
            $total = 0;

            // 2a. La commande elle-meme, avec un total a 0 pour l'instant
            $order = Order::create([
                'user_id'           => $request->user()->id,  // <- le lien avec le compte
                'reference'         => Order::generateReference(),
                'total'             => 0,
                'status'            => 'en_attente',
                'shipping_address'  => $data['shipping_address'],
                'shipping_city'     => $data['shipping_city'],
                'shipping_zip_code' => $data['shipping_zip_code'],
            ]);

            // 2b. Les lignes de commande, une par jeu
            foreach ($products as $product) {
                // Securite : je ne vends jamais plus que le stock reel,
                // meme si le cookie demande davantage.
                $qty = min($panier[$product->id], max($product->stock, 0));
                if ($qty < 1) {
                    continue;   // rupture entre-temps : on saute ce jeu
                }

                // attach() ecrit une ligne dans le pivot order_product.
                // Le deuxieme argument, ce sont les colonnes en plus.
                // Le prix est fige ici : c'est le prix du jour.
                $order->products()->attach($product->id, [
                    'quantity' => $qty,
                    'price'    => $product->price,
                ]);

                // decrement() fait "stock = stock - qty" directement en SQL
                $product->decrement('stock', $qty);

                $total += $product->price * $qty;
            }

            // 2c. Maintenant que je connais le total, je le mets a jour
            $order->update(['total' => $total]);

            return $order;
        });

        // 3. Le panier a servi, on efface le cookie.
        Cookie::queue(Cookie::forget(CartController::COOKIE_NAME));

        return redirect()->route('orders.show', $order)
                         ->with('success', "Commande {$order->reference} enregistree avec succes !");
    }

    /**
     * Annulation par le client.
     * Deux verrous : la commande doit etre la sienne, et elle doit
     * encore etre en attente. Une commande deja expediee ne s'annule pas.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'en_attente') {
            return back()->with('error', "Cette commande ne peut plus etre annulee.");
        }

        $order->update(['status' => 'annulee']);

        return back()->with('success', 'Commande annulee.');
    }
}
