@extends('layouts.admin')
@section('title', 'Gestion des jeux')

@section('actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-pq">+ Nouveau jeu</a>
@endsection

@section('content')
    <div class="pq-panel p-3">
        <div class="mb-3">
            <input type="text" id="table-filter" class="form-control" placeholder="Filtrer la liste...">
        </div>

        <div class="table-responsive" data-filterable>
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th></th><th>Nom</th><th>Categories</th><th>Plateforme</th>
                    <th class="text-end">Prix</th><th class="text-end">Stock</th><th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><img src="{{ $product->image_url }}" width="38" class="rounded" alt=""></td>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <div class="small text-muted">{{ $product->editor }}</div>
                        </td>
                        <td>
                            @foreach($product->categories as $c)
                                <span class="badge bg-secondary">{{ $c->name }}</span>
                            @endforeach
                        </td>
                        <td class="small">{{ $product->platform }}</td>
                        <td class="text-end">{{ number_format($product->price, 2, ',', ' ') }} &euro;</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $product->stock == 0 ? 'danger' : ($product->stock < 5 ? 'warning' : 'success') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Voir</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-pq">Modifier</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  class="d-inline" data-confirm="Supprimer « {{ $product->name }} » ?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Aucun jeu enregistre.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $products->links() }}</div>
@endsection
