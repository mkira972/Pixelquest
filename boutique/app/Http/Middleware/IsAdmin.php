<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LE MIDDLEWARE QUI PROTEGE L'ESPACE ADMIN.
 *
 * Un middleware, c'est un filtre qui s'execute AVANT le controleur.
 * Il a deux choix : laisser passer la requete, ou la bloquer.
 *
 * Celui-ci est enregistre sous l'alias 'admin' dans app/Http/Kernel.php,
 * ce qui me permet de l'appliquer a tout un groupe de routes en une ligne
 * dans routes/web.php :
 *
 *     Route::middleware(['auth', 'admin'])->prefix('admin')->group(...)
 *
 * A retenir pour l'oral : cacher le lien "Espace admin" dans le menu ne
 * protege rien du tout, il suffirait de taper l'URL a la main. La vraie
 * protection, c'est ce fichier.
 */
class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Deux verifications :
        // - $request->user() est null si personne n'est connecte
        // - isAdmin() lit la colonne is_admin de la table users
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, "Acces reserve aux administrateurs.");
        }

        // $next($request) = "c'est bon, continue vers le controleur"
        return $next($request);
    }
}
