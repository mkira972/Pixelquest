<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

/**
 * LA PAGE D'ACCUEIL.
 * Elle prepare trois listes et les envoie a la vue home.blade.php.
 */
class HomeController extends Controller
{
    /**
     * Page d'accueil de la boutique.
     */
    public function index(): View
    {
        return view('home', [
            // Les jeux mis en avant : ceux dont la case featured est
            // cochee dans l'admin. take(4) = LIMIT 4 en SQL.
            'featured'   => Product::with('categories')->where('featured', true)->take(4)->get(),

            // latest() trie par created_at du plus recent au plus ancien
            'nouveautes' => Product::with('categories')->latest()->take(8)->get(),

            // withCount ajoute products_count sans charger les jeux
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }
}
