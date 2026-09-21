@extends('layouts.app')
@section('title', 'Mon panier')

@section('content')
    <h1 class="h3 mb-4">Mon panier</h1>

    @if($products->isEmpty())
        <div class="pq-panel p-5 text-center">
            <p class="text-muted mb-3">Votre panier est vide.</p>
            <a href="{{ route('products.index') }}" class="btn btn-pq">Decouvrir le catalogue</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="pq-panel p-3">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Jeu</th>
                                <th class="text-end">Prix</th>
                                <th style="width: 170px">Quantite</th>
                                <th class="text-end">Sous-total</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                @php $qty = $panier[$product->id] ?? 0; @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $product->image_url }}" alt="" width="46" class="rounded">
                                            <div>
                                                <a href="{{ route('products.show', $product) }}"
                                                   class="text-decoration-none text-reset fw-semibold">{{ $product->name }}</a>
                                                <div class="small text-muted">{{ $product->platform }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ number_format($product->price, 2, ',', ' ') }} &euro;</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.update', $product) }}"
                                              class="d-flex gap-1">
                                            @csrf @method('PATCH')
                                            <input type="number" name="quantity" id="qty-{{ $product->id }}"
                                                   class="form-control form-control-sm" value="{{ $qty }}"
                                                   min="0" max="{{ max($product->stock, 1) }}">
                                            <button class="btn btn-sm btn-outline-pq" type="submit">OK</button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($product->price * $qty, 2, ',', ' ') }} &euro;
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('cart.remove', $product) }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.clear') }}" class="mt-3"
                      data-confirm="Voulez-vous vraiment vider le panier ?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Vider le panier</button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="pq-panel p-4">
                    <h2 class="h5 mb-3">Recapitulatif</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Articles</span>
                        <span>{{ array_sum($panier) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Livraison</span>
                        <span class="text-success">Offerte</span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                        <span>Total</span>
                        <span>{{ number_format($total, 2, ',', ' ') }} &euro;</span>
                    </div>

                    @auth
                        <a href="{{ route('orders.checkout') }}" class="btn btn-pq w-100">Valider ma commande</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-pq w-100">Se connecter pour commander</a>
                    @endauth
                </div>
            </div>
        </div>
    @endif
@endsection
