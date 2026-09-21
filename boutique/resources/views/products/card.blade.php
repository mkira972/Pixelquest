{{--
    Une vignette de jeu.
    Ce fichier est inclus depuis la page d'accueil et depuis le
    catalogue, comme ca je n'ecris la mise en forme qu'une fois.
--}}
<div class="pq-card d-flex flex-column">

    <a href="{{ route('products.show', $product) }}">
        <img src="{{ $product->image_url }}" class="pq-cover" alt="{{ $product->name }}">
    </a>

    <div class="p-3 d-flex flex-column flex-grow-1">

        <div class="small text-secondary mb-1">{{ $product->platform }}</div>

        <h3 class="h6 mb-2">
            <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-light">
                {{ $product->name }}
            </a>
        </h3>

        <div class="mt-auto d-flex align-items-center justify-content-between">
            <span class="pq-price">{{ number_format($product->price, 2, ',', ' ') }} &euro;</span>

            @if($product->isAvailable())
                <form method="POST" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <button class="btn btn-sm btn-pq" type="submit">Ajouter</button>
                </form>
            @else
                <span class="badge bg-danger">Rupture</span>
            @endif
        </div>
    </div>
</div>
