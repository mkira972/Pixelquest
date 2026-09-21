<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

/**
 * LE CATALOGUE PUBLIC.
 * Pas de middleware sur ces routes : tout le monde peut voir les jeux.
 */
class ProductController extends Controller
{
    /**
     * La liste de tous les jeux.
     *
     * with('categories') charge les categories de tous les jeux en une
     * seule requete, au lieu d'une par jeu.
     * latest() trie du plus recent au plus ancien.
     * paginate(12) coupe en pages de 12 jeux.
     */
    public function index(): View
    {
        $products = Product::with('categories')->latest()->paginate(12);

        return view('products.index', compact('products'));
    }

    /**
     * La fiche d'un jeu.
     *
     * Le parametre Product $product fait le travail tout seul : Laravel
     * prend l'id qui est dans l'URL, va chercher le jeu en base et me le
     * donne. Si l'id n'existe pas, il renvoie une erreur 404.
     */
    public function show(Product $product): View
    {
        $product->load('categories');

        return view('products.show', compact('product'));
    }

    /**
     * Les jeux d'une categorie.
     *
     * $category->products() passe par la relation Many to Many, donc par
     * la table pivot category_product. Je n'ecris aucune jointure SQL.
     */
    public function byCategory(Category $category): View
    {
        $products = $category->products()->with('categories')->paginate(12);
        $titre    = $category->name;

        return view('products.index', compact('products', 'titre'));
    }
}
