@extends('layouts.admin')
@section('title', 'Tableau de bord')

@section('content')
    <div class="row g-3 mb-4">
        @foreach([
            ['Jeux', $nbProducts, 'admin.products.index'],
            ['Categories', $nbCategories, 'admin.categories.index'],
            ['Commandes', $nbOrders, 'admin.orders.index'],
            ['Utilisateurs', $nbUsers, 'admin.users.index'],
        ] as [$label, $valeur, $route])
            <div class="col-6 col-lg-3">
                <a href="{{ route($route) }}" class="pq-panel p-4 d-block text-decoration-none text-reset h-100">
                    <div class="text-muted text-uppercase small mb-2">{{ $label }}</div>
                    <div class="pq-stat">{{ $valeur }}</div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="pq-panel p-4 mb-4">
        <div class="text-muted text-uppercase small mb-2">Chiffre d'affaires (commandes payees)</div>
        <div class="pq-stat text-success">{{ number_format($chiffre, 2, ',', ' ') }} &euro;</div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="pq-panel p-3">
                <h2 class="h6 text-muted text-uppercase px-2 pt-2">Dernieres commandes</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>Reference</th><th>Client</th><th>Statut</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                        @forelse($lastOrders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->reference }}</a></td>
                                <td>{{ $order->user?->name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                <td class="text-end">{{ number_format($order->total, 2, ',', ' ') }} &euro;</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Aucune commande.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="pq-panel p-3">
                <h2 class="h6 text-muted text-uppercase px-2 pt-2">Stocks faibles</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Jeu</th><th class="text-end">Stock</th></tr></thead>
                        <tbody>
                        @forelse($ruptures as $product)
                            <tr>
                                <td><a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a></td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">Tous les stocks sont bons.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
