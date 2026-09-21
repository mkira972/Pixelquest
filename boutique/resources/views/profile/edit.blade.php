@extends('layouts.app')
@section('title', 'Mon profil')

@section('content')
    <h1 class="h3 mb-4">Mon profil</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>

        <div class="col-lg-5">
            <div class="pq-panel p-4">
                <h2 class="h6 text-muted text-uppercase mb-3">Mes dernieres commandes</h2>

                @forelse($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="d-flex justify-content-between align-items-center py-2 text-decoration-none text-reset border-bottom border-secondary">
                        <div>
                            <div class="fw-semibold small">{{ $order->reference }}</div>
                            <div class="small text-muted">{{ $order->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-end">
                            <div>{{ number_format($order->total, 2, ',', ' ') }} &euro;</div>
                            <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-muted small mb-0">Aucune commande pour le moment.</p>
                @endforelse

                <a href="{{ route('orders.index') }}" class="btn btn-outline-pq w-100 mt-3">Toutes mes commandes</a>
            </div>
        </div>
    </div>
@endsection
