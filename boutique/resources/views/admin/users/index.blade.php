@extends('layouts.admin')
@section('title', 'Gestion des utilisateurs')

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-pq">+ Nouvel utilisateur</a>
@endsection

@section('content')
    <div class="pq-panel p-3">
        <div class="mb-3">
            <input type="text" id="table-filter" class="form-control" placeholder="Filtrer la liste...">
        </div>

        <div class="table-responsive" data-filterable>
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr><th>Nom</th><th>E-mail</th><th>Ville</th><th>Role</th>
                    <th class="text-end">Commandes</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}</td>
                        <td class="small">{{ $u->email }}</td>
                        <td class="small text-muted">{{ $u->city ?: '-' }}</td>
                        <td>
                            @if($u->is_admin)
                                <span class="badge bg-warning text-dark">Admin</span>
                            @else
                                <span class="badge bg-secondary">Client</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $u->orders_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-pq">Modifier</a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                      class="d-inline" data-confirm="Supprimer « {{ $u->name }} » et toutes ses commandes ?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Aucun utilisateur.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
