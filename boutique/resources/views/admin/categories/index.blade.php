@extends('layouts.admin')
@section('title', 'Gestion des categories')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-pq">+ Nouvelle categorie</a>
@endsection

@section('content')
    <div class="pq-panel p-3">
        <div class="mb-3">
            <input type="text" id="table-filter" class="form-control" placeholder="Filtrer la liste...">
        </div>

        <div class="table-responsive" data-filterable>
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr><th>Nom</th><th>Slug</th><th>Description</th><th class="text-end">Jeux</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse($categories as $categorie)
                    <tr>
                        <td class="fw-semibold">{{ $categorie->name }}</td>
                        <td class="small text-muted">{{ $categorie->slug }}</td>
                        <td class="small text-muted">{{ Str::limit($categorie->description, 70) }}</td>
                        <td class="text-end"><span class="badge bg-secondary">{{ $categorie->products_count }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('categories.show', $categorie) }}" class="btn btn-sm btn-outline-secondary" target="_blank">Voir</a>
                            <a href="{{ route('admin.categories.edit', $categorie) }}" class="btn btn-sm btn-outline-pq">Modifier</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $categorie) }}"
                                  class="d-inline" data-confirm="Supprimer la categorie « {{ $categorie->name }} » ?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Aucune categorie.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
@endsection
