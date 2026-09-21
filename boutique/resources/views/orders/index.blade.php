@extends('layouts.app')
@section('title', 'Mes commandes')

@section('content')
    <h1 class="h3 mb-4">Mes commandes</h1>

    @if($orders->isEmpty())
        <div class="pq-panel p-5 text-center">
            <p class="text-muted mb-3">Vous n'avez pas encore passe de commande.</p>
            <a href="{{ route('products.index') }}" class="btn btn-pq">Voir le catalogue</a>
        </div>
    @else
        <div class="pq-panel p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Articles</th>
                        <th>Statut</th>
                        <th class="text-end">Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->reference }}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>{{ $order->total_items }}</td>
                            <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td class="text-end">{{ number_format($order->total, 2, ',', ' ') }} &euro;</td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-pq">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection
