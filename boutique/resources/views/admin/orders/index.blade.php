@extends('layouts.admin')
@section('title', 'Gestion des commandes')

@section('content')
    <div class="pq-panel p-3">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $cle => $libelle)
                        <option value="{{ $cle }}" @selected($current === $cle)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="table-filter" class="form-control" placeholder="Filtrer la liste...">
            </div>
        </form>

        <div class="table-responsive" data-filterable>
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr><th>Reference</th><th>Client</th><th>Date</th><th>Statut</th>
                    <th class="text-end">Total</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-semibold">{{ $order->reference }}</td>
                        <td>
                            {{ $order->user?->name ?? 'Compte supprime' }}
                            <div class="small text-muted">{{ $order->user?->email }}</div>
                        </td>
                        <td class="small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                        <td class="text-end">{{ number_format($order->total, 2, ',', ' ') }} &euro;</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-outline-pq">Modifier</a>
                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                  class="d-inline" data-confirm="Supprimer la commande {{ $order->reference }} ?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Aucune commande.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
@endsection
