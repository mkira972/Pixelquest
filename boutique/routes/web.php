<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ---------------------- 1. Routes publiques ---------------------- */
/* Accessibles sans compte. C'est la vitrine de la boutique. */

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalogue', [ProductController::class, 'index'])->name('products.index');
Route::get('/jeu/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categorie/{category}', [ProductController::class, 'byCategory'])->name('categories.show');

/* ------------------- Le panier, stocke en cookie -------------------- */
/*
 * Volontairement public : un visiteur peut remplir son panier avant
 * de se connecter. C'est seulement pour VALIDER la commande qu'il
 * faudra un compte.
 *
 * Les verbes HTTP ne sont pas choisis au hasard :
 *   GET    = lire        POST   = creer
 *   PATCH  = modifier    DELETE = supprimer
 */

Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/modifier/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/retirer/{product}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/vider', [CartController::class, 'clear'])->name('clear');
});

/* ---------- 2. Routes protegees : utilisateur connecte ------------- */
/*
 * Le middleware 'auth' bloque tout visiteur non connecte et le renvoie
 * vers la page de connexion. C'est le "routes protegees (auth only)"
 * du cahier des charges.
 */

Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Commandes
    Route::get('/commande/validation', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/commande', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/mes-commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mes-commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/mes-commandes/{order}/annuler', [OrderController::class, 'cancel'])->name('orders.cancel');
});

/* ------------- 3. Espace admin : routes protegees admin ------------- */
/*
 * Deux middlewares a la suite : 'auth' verifie qu'on est connecte,
 * puis 'admin' (mon middleware IsAdmin) verifie la colonne is_admin.
 * Un client connecte qui tape /admin recoit une erreur 403.
 *
 * prefix('admin') ajoute /admin devant toutes les URL du groupe.
 * name('admin.') ajoute admin. devant tous les noms de routes.
 * Resultat : route('admin.products.index') pointe sur /admin/products.
 */

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // /admin n'a pas de page a lui : on envoie directement sur la
        // liste des jeux. Je garde le nom 'admin.dashboard' pour que les
        // liens deja ecrits dans les vues continuent de marcher.
        Route::get('/', function () {
            return redirect()->route('admin.products.index');
        })->name('dashboard');

        // except('show') : pas besoin d'une page de detail cote admin,
        // la page de modification affiche deja tout.
        Route::resource('products', AdminProductController::class)->except('show');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('users', AdminUserController::class)->except('show');
        // Pour les commandes, pas de create ni de store : une commande
        // est creee par un client depuis la boutique, jamais a la main
        // par l'admin. Il peut seulement les consulter et les modifier.
        Route::resource('orders', AdminOrderController::class)->except('create', 'store');
    });

// Les routes de connexion, inscription, deconnexion et mot de passe
// oublie sont dans un fichier a part, fourni par Laravel Breeze.
require __DIR__.'/auth.php';
