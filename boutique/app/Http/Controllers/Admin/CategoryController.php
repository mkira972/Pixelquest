<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD DES CATEGORIES, COTE ADMIN.
 *
 * Ce controleur est le miroir de ProductController. C'est volontaire :
 * le cahier des charges demandait les deux sens de la relation.
 *
 *   ProductController  -> modifier les categories d'un article
 *   CategoryController -> modifier les articles d'une categorie
 *
 * Et dans les deux cas, c'est la MEME table pivot category_product qui
 * est ecrite. C'est ca, une relation Many to Many : elle n'appartient a
 * aucun des deux modeles, elle est entre les deux.
 */
class CategoryController extends Controller
{
    /**
     * La liste des categories.
     * withCount('products') ajoute une colonne calculee products_count
     * avec le nombre de jeux de chaque categorie, sans charger les jeux
     * eux-memes. Dans la vue j'ecris $categorie->products_count.
     */
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->paginate(15),
        ]);
    }

    /**
     * Le formulaire de creation.
     * Je charge TOUS les jeux pour pouvoir afficher la liste de cases a
     * cocher. Sur un vrai site avec des milliers de produits il faudrait
     * une recherche cote serveur, mais ici 16 jeux ca passe tres bien.
     */
    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valider($request);

        $category = Category::create($data);

        // ICI : "modifier les articles d'une categorie" du cahier des charges.
        // Exactement le meme sync() que dans ProductController, mais pris
        // dans l'autre sens : je pars de la categorie vers les jeux.
        $category->products()->sync($request->input('products', []));

        return redirect()->route('admin.categories.index')
                         ->with('success', "La categorie « {$category->name} » a ete creee.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category->load('products'),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->valider($request, $category);

        $category->update($data);

        // Remplace toute la liste des jeux de cette categorie par celle
        // qui vient d'etre cochee dans le formulaire.
        $category->products()->sync($request->input('products', []));

        return redirect()->route('admin.categories.index')
                         ->with('success', "La categorie « {$category->name} » a ete modifiee.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $nom = $category->name;

        // Je coupe d'abord les liens dans le pivot, puis je supprime la
        // categorie. Les jeux, eux, ne sont pas supprimes : ils perdent
        // juste cette categorie.
        $category->products()->detach();
        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', "La categorie « {$nom} » a ete supprimee.");
    }

    /** Validation commune a store() et update(). */
    private function valider(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255',
                               Rule::unique('categories')->ignore($category?->id)],
            'description' => ['nullable', 'string'],
            'products'    => ['nullable', 'array'],
            'products.*'  => ['integer', 'exists:products,id'],
        ]);

        // products n'est pas une colonne de la table categories,
        // c'est sync() qui le traite. Je l'enleve avant le create/update.
        unset($data['products']);

        return $data;
    }
}
