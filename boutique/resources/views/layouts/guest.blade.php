<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon compte') — PixelQuest</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="pq-brand fs-2 text-decoration-none">PixelQuest</a>
                <p class="text-muted mb-0">@yield('subtitle', 'Votre boutique de jeux video')</p>
            </div>

            <div class="pq-panel p-4">
                @include('partials.flash')
                @yield('content')
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="{{ route('home') }}" class="small">Retour a la boutique</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
