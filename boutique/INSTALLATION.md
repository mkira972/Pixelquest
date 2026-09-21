# PixelQuest — boutique de jeux vidéo (Laravel 10)

Tous les fichiers ont été écrits dans `UwAmp\www\boutique`. Il reste **4 étapes** à faire de ton côté.

---

## Étape 1 — Modifier le `.env` à la main

Les outils distants ne peuvent pas écrire dans un `.env`. Ouvre-le et change ces 3 lignes :

```env
APP_NAME=PixelQuest
APP_URL=http://localhost/boutique/public
MAIL_MAILER=log
```

`MAIL_MAILER=log` fait écrire le mail de réinitialisation (avec le lien + token) dans
`storage/logs/laravel.log` — parfait pour la démo, aucun SMTP à configurer.

## Étape 2 — Installer Bootstrap et compiler les assets

```bash
cd C:\Users\manio\Downloads\UwAmp_3.0.2\UwAmp\www\boutique
npm install
npm run build
```

`package.json` a été remplacé : Tailwind est parti, Bootstrap 5.3 + Popper sont arrivés.
Pendant que tu développes, `npm run dev` est plus pratique (rechargement auto).

## Étape 3 — Base de données vierge + données de test

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
```

## Étape 4 — Supprimer 4 fichiers parasites

À la racine de `boutique` traînent 4 fichiers vides de 0 octet créés par une commande
PowerShell mal interprétée. Supprime-les (ils ne cassent rien mais font désordre sur le rendu) :

`id()` · `default(1)` · `onDelete('cascade')` · `timestamps()`

---

## Comptes de démonstration

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Administrateur | `admin@pixelquest.fr` | `password` |
| Client | `client@pixelquest.fr` | `password` |

Site : `http://localhost/boutique/public` — Admin : `http://localhost/boutique/public/admin`

---

## Le cahier des charges, point par point

| Attendu | Où c'est fait |
|---|---|
| Page d'accueil | `HomeController` + `views/home.blade.php` (hero, sélection, catégories, nouveautés) |
| Connexion / inscription / déconnexion | Breeze, vues réécrites en Bootstrap dans `views/auth/` |
| Routes protégées (auth only) | `routes/web.php` → `Route::middleware('auth')` (profil, commandes, checkout) |
| Oubli de mot de passe (mail + lien + token périssable) | Breeze `password.request` / `password.email` / `password.store`, token 60 min |
| Page profil : modification + suppression du compte | `ProfileController` + `views/profile/` (modale de confirmation par mot de passe) |
| Ajout de produits à une commande, lié au compte, **gestion de cookies** | `CartController` — panier stocké dans un cookie JSON 30 jours, transformé en commande liée au `user_id` au checkout |
| **Relation BDD Many to Many** | 2 relations : `category_product` (jeux ↔ catégories) et `order_product` (commandes ↔ jeux, avec `quantity` et `price` sur le pivot) |
| Enregistrement de commande | `OrderController@store` — transaction, décrément du stock, pivot, référence unique |
| Compte admin, routes protégées admin, auth admin | colonne `is_admin` + middleware `App\Http\Middleware\IsAdmin` alias `admin` |
| Espace admin | `layouts/admin.blade.php` + tableau de bord avec stats, dernières commandes, stocks faibles |
| CRUD pour chaque table dynamique | `Admin\ProductController`, `Admin\CategoryController`, `Admin\OrderController`, `Admin\UserController` |
| Modifier les catégories d'un article (admin) | formulaire produit → cases à cocher → `$product->categories()->sync(...)` |
| Modifier les articles d'une catégorie (admin) | formulaire catégorie → liste filtrable de jeux → `$category->products()->sync(...)` |
| Jeu de migrations complet depuis BDD vierge | 6 migrations, ordre de dépendances validé, `migrate:fresh` fonctionne |
| Données de test via Seeder | `UserSeeder`, `CategorySeeder`, `ProductSeeder`, `OrderSeeder` (7 comptes, 10 catégories, 16 jeux, commandes aléatoires) |
| Architecture MVC propre | Models / Controllers / Views séparés, validation dans les contrôleurs, logique métier dans les modèles |
| Utilisation de Bootstrap | Bootstrap 5.3 via npm + Vite, thème sombre personnalisé dans `resources/css/app.css` |
| Design cohérent et responsive | grille Bootstrap, navbar collapse, tableaux `table-responsive`, testé du mobile au desktop |
| Repo Git à jour | à faire : `git add . && git commit -m "Boutique PixelQuest"` |

---

## Carte des routes

**Public** — `/` · `/catalogue` · `/jeu/{id}` · `/categorie/{id}` · `/panier`

**Connecté** — `/profil` · `/commande/validation` · `/mes-commandes` · `/mes-commandes/{id}`

**Admin** — `/admin` · `/admin/products` · `/admin/categories` · `/admin/orders` · `/admin/users`

---

## Tester la réinitialisation du mot de passe (pour la démo)

1. Déconnecte-toi, va sur `/login` → « Mot de passe oublié ? »
2. Saisis `client@pixelquest.fr`
3. Ouvre `storage/logs/laravel.log`, descends tout en bas
4. Copie l'URL `http://localhost/boutique/public/reset-password/<token>?email=...` dans le navigateur
5. Choisis un nouveau mot de passe — le token expire au bout de 60 minutes

---

## Si quelque chose casse

| Symptôme | Cause / solution |
|---|---|
| Page blanche ou « Vite manifest not found » | `npm run build` n'a pas été lancé |
| Le site s'affiche sans aucun style | `APP_URL` ne pointe pas sur `http://localhost/boutique/public` |
| « Specified key was too long » à la migration | `Schema::defaultStringLength(191)` est bien dans `AppServiceProvider` — vérifie qu'il y est |
| 403 sur `/admin` | le compte n'a pas `is_admin = 1` |
| Anciennes vues Tailwind qui réapparaissent | `php artisan view:clear` |
