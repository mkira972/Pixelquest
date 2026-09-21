<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // COMPATIBILITE MYSQL 5.6 (celui d'UwAmp).
        // MySQL 5.6 limite les index a 767 octets. Or Laravel cree par
        // defaut des VARCHAR(255) en utf8mb4, soit 255 x 4 = 1020 octets,
        // et la migration plante avec "Specified key was too long".
        // 191 x 4 = 764 octets, ca passe juste.
        Schema::defaultStringLength(191);

        // Par defaut la pagination de Laravel sort du HTML en Tailwind.
        // Cette ligne la fait sortir en Bootstrap 5, sinon les boutons
        // "page suivante" n'auraient aucun style.
        Paginator::useBootstrapFive();

        // UN VIEW COMPOSER : a chaque fois que le gabarit layouts.app est
        // affiche, Laravel execute cette fonction et lui passe la variable
        // $navCategories. Ca me sert pour le menu deroulant "Categories"
        // de la navbar, qui apparait sur toutes les pages publiques.
        // Sans ca, il faudrait que CHAQUE controleur pense a envoyer les
        // categories a sa vue.
        View::composer('layouts.app', function ($view) {
            $view->with('navCategories', Category::orderBy('name')->get());
        });
    }
}
