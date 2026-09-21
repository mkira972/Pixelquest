<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD DES JEUX, COTE ADMIN.
 *
 * Toutes ces routes sont declarees avec Route::resource() et protegees
 * par les middlewares 'auth' et 'admin' (voir routes/web.php).
 *
 * Route::resource cree d'un coup les 7 routes REST standard :
 *   index   GET    /admin/products              la liste
 *   create  GET    /admin/products/create       le formulaire de creation
 *   store   POST   /admin/products              enregistre le nouveau jeu
 *   edit    GET    /admin/products/{id}/edit    le formulaire de modification
 *   update  PUT    /admin/products/{id}         enregistre les modifications
 *   destroy DELETE /admin/products/{id}         supprime
 *
 * Les noms des methodes ci-dessous doivent correspondre exactement,
 * sinon Laravel ne les trouve pas.
 */
class ProductController extends Controller
{
    /** La liste des jeux, avec un champ de recherche. */
    public function index(Request $request): View
    {
        // with('categories') charge les categories de tous les jeux en
        // UNE requete au lieu d'une par jeu (probleme du N+1).
        $query = Product::with('categories');

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.products.index', [
            'products' => $query->latest()->paginate(15)->withQueryString(),
            'search'   => $search,
        ]);
    }

    /**
     * Le formulaire de creation.
     * Je passe un new Product() vide pour pouvoir reutiliser le meme
     * fichier de formulaire (_form.blade.php) pour la creation ET la
     * modification. Dedans je teste $product->exists pour savoir
     * lequel des deux cas on est.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'product'    => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valider($request);

        // Si l'admin a joint une image, on l'enregistre sur le disque et
        // on met son chemin dans la colonne image.
        if ($request->hasFile('image')) {
            $data['image'] = $this->enregistrerImage($request->file('image'));
        }

        $product = Product::create($data);

        // ICI : "modifier les categories d'un article" du cahier des charges.
        // Le formulaire envoie un tableau d'ids coches, genre [1, 4, 7].
        // sync() ecrit ces liens dans la table pivot category_product.
        // Le [] par defaut sert au cas ou l'admin ne coche rien : sans lui
        // input() renverrait null et sync() planterait.
        $product->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.products.index')
                         ->with('success', "Le jeu « {$product->name} » a ete cree.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product'    => $product->load('categories'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->valider($request, $product);

        // Nouvelle image envoyee : on la stocke et on efface l'ancienne.
        if ($request->hasFile('image')) {
            $this->supprimerImage($product->image);
            $data['image'] = $this->enregistrerImage($request->file('image'));
        }

        // Case « supprimer l'image » cochee, sans nouveau fichier.
        if ($request->boolean('supprimer_image') && ! $request->hasFile('image')) {
            $this->supprimerImage($product->image);
            $data['image'] = null;
        }

        $product->update($data);

        // sync() REMPLACE tout le contenu du pivot pour ce jeu.
        // C'est pour ca que je l'utilise plutot que attach() : il ajoute
        // les categories nouvellement cochees ET retire celles qui ont ete
        // decochees, en un seul appel. attach() ne ferait qu'ajouter.
        $product->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.products.index')
                         ->with('success', "Le jeu « {$product->name} » a ete modifie.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $nom = $product->name;

        // detach() sans argument vide toutes les lignes du pivot pour ce jeu.
        // La cle etrangere est deja en onDelete('cascade'), donc MySQL le
        // ferait aussi, mais je prefere etre explicite.
        $product->categories()->detach();

        // On efface aussi le fichier image, sinon il resterait sur le
        // disque sans plus aucune ligne en base qui le referme.
        $this->supprimerImage($product->image);

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', "Le jeu « {$nom} » a ete supprime.");
    }

    /**
     * La validation, mise en commun entre store() et update().
     *
     * Le ?Product en parametre sert a la regle unique sur le slug :
     * en modification, il faut ignorer le jeu en cours, sinon il se
     * declare lui-meme en doublon avec son propre slug.
     */
    private function valider(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255',
                               Rule::unique('products')->ignore($product?->id)],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'stock'       => ['required', 'integer', 'min:0'],
            // image est maintenant un FICHIER envoye par le formulaire.
            // 'image'      -> Laravel verifie que c'est bien une image
            // 'mimes'      -> seuls ces formats sont acceptes
            // 'max:2048'   -> 2048 Ko, donc 2 Mo maximum
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'platform'    => ['nullable', 'string', 'max:60'],
            'editor'      => ['nullable', 'string', 'max:100'],
            'pegi'        => ['nullable', 'integer', 'min:3', 'max:18'],
            'categories'  => ['nullable', 'array'],
            'categories.*'=> ['integer', 'exists:categories,id'],
        ]);

        // Une case a cocher non cochee n'est pas envoyee du tout par le
        // navigateur. boolean() renvoie false dans ce cas au lieu de null.
        $data['featured'] = $request->boolean('featured');

        // Je retire categories du tableau : ce n'est pas une colonne de
        // la table products, c'est sync() qui s'en occupe a part.
        unset($data['categories']);

        // Et je retire image : le fichier est traite a part par
        // enregistrerImage(). Sans ca, Laravel essaierait d'enregistrer
        // l'objet fichier lui-meme dans la colonne.
        unset($data['image']);

        return $data;
    }

    /**
     * ENREGISTRE LE FICHIER ENVOYE et renvoie son chemin.
     *
     * Le fichier part dans public/uploads/products/. J'ai choisi ce
     * dossier plutot que storage/app/public parce que celui-la demande
     * un lien symbolique (php artisan storage:link), et sous Windows ce
     * lien echoue souvent faute de droits. Ici, aucune commande a lancer.
     *
     * Le nom du fichier est reconstruit a partir du nom d'origine :
     *   "Ma Photo (1).JPG"  ->  "ma-photo-1-6f3a2b9c1d.jpg"
     * Je ne garde jamais le nom tel quel. Un nom de fichier vient du
     * visiteur, il peut contenir des accents, des espaces, ou pire des
     * "../" pour tenter d'ecrire ailleurs sur le disque.
     */
    private function enregistrerImage(UploadedFile $fichier): string
    {
        $base = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);

        $nom = Str::slug($base) . '-' . Str::random(10) . '.' . $fichier->extension();

        $fichier->move(public_path('uploads/products'), $nom);

        // C'est ce chemin relatif qui est stocke en base
        return 'uploads/products/' . $nom;
    }

    /**
     * Efface un fichier image du disque.
     * Ne touche a rien si la valeur est vide ou si c'est une URL externe
     * (les jeux du seeder pointent vers une image en ligne).
     */
    private function supprimerImage(?string $chemin): void
    {
        if (! $chemin || Str::startsWith($chemin, ['http://', 'https://'])) {
            return;
        }

        $fichier = public_path($chemin);

        if (is_file($fichier)) {
            @unlink($fichier);
        }
    }
}
