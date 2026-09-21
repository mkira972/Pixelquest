@extends('layouts.app')
@section('title', 'Validation de la commande')

@section('content')
    <h1 class="h3 mb-4">Validation de la commande</h1>

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="pq-panel p-4">
                    <h2 class="h5 mb-3">Adresse de livraison</h2>

                    <div class="mb-3">
                        <label class="form-label" for="shipping_address">Adresse</label>
                        <input type="text" id="shipping_address" name="shipping_address"
                               class="form-control @error('shipping_address') is-invalid @enderror"
                               value="{{ old('shipping_address', auth()->user()->address) }}" required>
                        @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="shipping_city">Ville</label>
                            <input type="text" id="shipping_city" name="shipping_city"
                                   class="form-control @error('shipping_city') is-invalid @enderror"
                                   value="{{ old('shipping_city', auth()->user()->city) }}" required>
                            @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="shipping_zip_code">Code postal</label>
                            <input type="text" id="shipping_zip_code" name="shipping_zip_code"
                                   class="form-control @error('shipping_zip_code') is-invalid @enderror"
                                   value="{{ old('shipping_zip_code', auth()->user()->zip_code) }}" required>
                            @error('shipping_zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="pq-panel p-4">
                    <h2 class="h5 mb-3">Votre commande</h2>
                    <ul class="list-unstyled mb-3">
                        @foreach($products as $product)
                            @php $qty = $panier[$product->id] ?? 0; @endphp
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ $qty }} &times; {{ Str::limit($product->name, 24) }}</span>
                                <span>{{ number_format($product->price * $qty, 2, ',', ' ') }} &euro;</span>
                            </li>
                        @endforeach
                    </ul>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                        <span>Total</span>
                        <span>{{ number_format($total, 2, ',', ' ') }} &euro;</span>
                    </div>
                    <button type="submit" class="btn btn-pq w-100">Enregistrer la commande</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-2">Modifier le panier</a>
                </div>
            </div>
        </div>
    </form>
@endsection
