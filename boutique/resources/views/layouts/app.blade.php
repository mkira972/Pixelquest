<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PixelQuest') — PixelQuest</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
{{--
    Le petit chiffre rouge a cote de "Panier" dans la navbar.
    Je relis le cookie a chaque affichage pour avoir le nombre exact
    d'articles. array_sum additionne les quantites : 2 exemplaires d'un
    jeu + 1 d'un autre affichent 3, et pas 2.
--}}
@php
    $panier = \App\Http\Controllers\CartController::panier(request());
    $nbArticles = array_sum($panier);
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary sticky-top">
    <div class="container">
        <a class="navbar-brand pq-brand fs-4" href="{{ route('home') }}">PixelQuest</a>

        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-label="Menu">
            <span class="navbar-toggler-icon" style="filter: invert(1)"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                       href="{{ route('products.index') }}">Catalogue</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Categories
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(($navCategories ?? collect()) as $c)
                            <li><a class="dropdown-item" href="{{ route('categories.show', $c) }}">{{ $c->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                        Panier
                        @if($nbArticles > 0)
                            <span class="badge rounded-pill bg-danger">{{ $nbArticles }}</span>
                        @endif
                    </a>
                </li>

                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm btn-pq" href="{{ route('register') }}">Inscription</a>
                    </li>
                {{-- @guest / @else / @endguest : Blade affiche le premier
                     bloc si personne n'est connecte, le second sinon. --}}
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Str::limit(auth()->user()->name, 18) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}">Mes commandes</a></li>
                            {{-- Le lien vers l'admin n'apparait que pour un
                                 administrateur. Attention : ca ne protege
                                 rien, c'est juste de l'affichage. La vraie
                                 protection est le middleware IsAdmin. --}}
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-warning" href="{{ route('admin.dashboard') }}">Espace admin</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Deconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        @include('partials.flash')
        @yield('content')
    </div>
</main>

</body>
</html>
