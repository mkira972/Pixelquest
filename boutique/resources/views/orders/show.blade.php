@extends('layouts.app')
@section('title', 'Commande ' . $order->reference)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Commande {{ $order->reference }}</h1>
        <span class="badge bg-{{ $order->status_color }} fs-6">{{ $order->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="pq-panel p-3">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Jeu</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-center">Qte</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $product->image_url }}" alt="" width="42" class="rounded">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="text-decoration-none text-reset">{{ $product->name }}</a>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($product->pivot->price, 2, ',', ' ') }} &euro;</td>
                                <td class="text-center">{{ $product->pivot->quantity }}</td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', ' ') }} &euro;
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end fs-5">{{ number_format($order->total, 2, ',', ' ') }} &euro;</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="pq-panel p-4 mb-3">
                <h2 class="h6 text-muted text-uppercase mb-3">Livraison</h2>
                <p class="mb-1">{{ $order->shipping_address }}</p>
                <p class="mb-0">{{ $order->shipping_zip_code }} {{ $order->shipping_city }}</p>
            </div>

            <div class="pq-panel p-4">
                <h2 class="h6 text-muted text-uppercase mb-3">Informations</h2>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Passee le</span>
                    <span>{{ $order->created_at->format('d/m/Y a H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Articles</span>
                    <span>{{ $order->total_items }}</span>
                </div>

                @if($order->status === 'en_attente')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}"
                          data-confirm="Annuler definitivement cette commande ?">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-danger w-100" type="submit">Annuler la commande</button>
                    </form>
                @endif
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-link w-100 mt-2">Retour a mes commandes</a>
        </div>
    </div>
@endsection
