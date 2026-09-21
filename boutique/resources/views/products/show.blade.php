@extends('layouts.app')
@section('title', $product->name)

@section('content')
    <nav aria-label="fil d'ariane" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Catalogue</a></li>
            <li class="breadcrumb-item active text-muted">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <img src="{{ $product->image_url }}" class="img-fluid rounded-3 w-100" alt="{{ $product->name }}">
        </div>

        <div class="col-lg-6">
            <div class="pq-panel p-4 h-100">
                <div class="mb-2">
                    @foreach($product->categories as $c)
                        <a href="{{ route('categories.show', $c) }}" class="badge bg-secondary text-decoration-none">{{ $c->name }}</a>
                    @endforeach
                </div>

                <h1 class="h3">{{ $product->name }}</h1>
                <p class="text-muted mb-3">
                    {{ $product->platform }}
                    @if($product->editor) &middot; {{ $product->editor }} @endif
                    @if($product->pegi) &middot; PEGI {{ $product->pegi }} @endif
                </p>

                <p>{{ $product->description }}</p>

                <hr class="border-secondary">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="display-6 fw-bold text-white">{{ number_format($product->price, 2, ',', ' ') }} &euro;</div>
                        @if($product->isAvailable())
                            <span class="text-success small">En stock ({{ $product->stock }} exemplaires)</span>
                        @else
                            <span class="text-danger small">Rupture de stock</span>
                        @endif
                    </div>

                    @if($product->isAvailable())
                        <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex gap-2">
                            @csrf
                            <input type="number" name="quantity" class="form-control" style="width: 90px"
                                   value="1" min="1" max="{{ min($product->stock, 99) }}">
                            <button class="btn btn-pq btn-lg" type="submit">Ajouter au panier</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
