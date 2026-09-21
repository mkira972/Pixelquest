@extends('layouts.admin')
@section('title', 'Commande ' . $order->reference)

@section('actions')
    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-pq">Modifier</a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="pq-panel p-3">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr><th>Jeu</th><th class="text-end">Prix</th><th class="text-center">Qte</th><th class="text-end">Sous-total</th></tr>
                        </thead>
                        <tbody>
                        @foreach($order->products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $product->image_url }}" width="38" class="rounded" alt="">
                                        <span>{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($product->pivot->price, 2, ',', ' ') }} &euro;</td>
                                <td class="text-center">{{ $product->pivot->quantity }}</td>
                                <td class="text-end">{{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', ' ') }} &euro;</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr><th colspan="3" class="text-end">Total</th>
                            <th class="text-end fs-5">{{ number_format($order->total, 2, ',', ' ') }} &euro;</th></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="pq-panel p-4 mb-3">
                <h2 class="h6 text-muted text-uppercase mb-3">Client</h2>
                <p class="mb-1 fw-semibold">{{ $order->user?->name ?? 'Compte supprime' }}</p>
                <p class="small text-muted mb-2">{{ $order->user?->email }}</p>
                @if($order->user)
                    <a href="{{ route('admin.users.edit', $order->user) }}" class="small">Voir la fiche client</a>
                @endif
            </div>

            <div class="pq-panel p-4 mb-3">
                <h2 class="h6 text-muted text-uppercase mb-3">Livraison</h2>
                <p class="mb-1">{{ $order->shipping_address ?: '-' }}</p>
                <p class="mb-0">{{ $order->shipping_zip_code }} {{ $order->shipping_city }}</p>
            </div>

            <div class="pq-panel p-4">
                <h2 class="h6 text-muted text-uppercase mb-3">Statut</h2>
                <span class="badge bg-{{ $order->status_color }} fs-6 mb-3">{{ $order->status_label }}</span>
                <div class="small text-muted">Passee le {{ $order->created_at->format('d/m/Y a H:i') }}</div>
            </div>
        </div>
    </div>
@endsection
