@extends('layouts.app')
@section('title', 'Accueil')

@section('content')
    <section class="pq-panel p-4 p-lg-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-secondary mb-2">Nouvelle saison</span>
                <h1 class="display-5 fw-bold mb-3">Tous vos jeux video<br>au meilleur prix.</h1>
                <p class="text-muted mb-4">
                    {{ $categories->sum('products_count') }} jeux en stock, sur PC, PlayStation, Xbox et Switch.
                    Livraison en 48h et retours gratuits sous 14 jours.
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-pq btn-lg px-4">Voir le catalogue</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-pq btn-lg px-4 ms-2">Creer un compte</a>
                @endguest
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                {{-- Deux jeux mis en avant, en format paysage --}}
                <div class="d-flex flex-column gap-3">
                    @foreach($featured->take(2) as $jeu)
                        <a href="{{ route('products.show', $jeu) }}">
                            <img src="{{ $jeu->image_url }}" class="img-fluid rounded-3 w-100" alt="{{ $jeu->name }}">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if($featured->isNotEmpty())
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 class="h4 mb-0">Selection de la redaction</h2>
            <a href="{{ route('products.index') }}" class="small">Tout voir</a>
        </div>
        <div class="row g-4 mb-5">
            @foreach($featured as $product)
                <div class="col-12 col-sm-6 col-lg-4">
                    @include('products.card', ['product' => $product])
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="h4 mb-3">Parcourir par categorie</h2>
    <div class="row g-3 mb-5">
        @foreach($categories as $categorie)
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('categories.show', $categorie) }}"
                   class="pq-panel d-block p-3 text-decoration-none h-100">
                    <div class="fw-semibold text-white">{{ $categorie->name }}</div>
                    <div class="small text-muted">{{ $categorie->products_count }} jeu(x)</div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 mb-0">Dernieres nouveautes</h2>
        <a href="{{ route('products.index') }}" class="small">Tout voir</a>
    </div>
    <div class="row g-4">
        @foreach($nouveautes as $product)
            <div class="col-12 col-sm-6 col-lg-4">
                @include('products.card', ['product' => $product])
            </div>
        @endforeach
    </div>
@endsection
