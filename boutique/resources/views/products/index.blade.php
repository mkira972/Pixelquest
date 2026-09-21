@extends('layouts.app')
@section('title', $titre ?? 'Catalogue')

@section('content')
    <h1 class="h3 mb-4">{{ $titre ?? 'Catalogue' }}</h1>

    <p class="text-secondary">{{ $products->total() }} jeu(x)</p>

    @if($products->isEmpty())
        <div class="pq-panel p-5 text-center text-secondary">
            Aucun jeu pour le moment.
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-lg-4">
                    @include('products.card', ['product' => $product])
                </div>
            @endforeach
        </div>

        {{-- links() affiche les boutons de pagination --}}
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
@endsection
