@extends('layouts.admin')
@section('title', 'Modifier la commande ' . $order->reference)

@section('content')
    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="pq-panel p-3 mb-4">
                    <h2 class="h6 text-muted text-uppercase px-2 pt-2">Lignes de commande</h2>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Jeu</th><th class="text-end">Prix</th><th style="width:130px">Quantite</th></tr></thead>
                            <tbody>
                            @forelse($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end">{{ number_format($product->pivot->price, 2, ',', ' ') }} &euro;</td>
                                    <td>
                                        <input type="number" min="0" max="99"
                                               name="lignes[{{ $product->id }}][quantity]"
                                               value="{{ $product->pivot->quantity }}"
                                               class="form-control form-control-sm">
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Aucune ligne.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pq-panel p-4">
                    <h2 class="h6 text-muted text-uppercase mb-3">Ajouter un jeu a la commande</h2>
                    <div class="row g-2">
                        <div class="col-md-8">
                            <select name="add_product_id" class="form-select">
                                <option value="">-- Choisir un jeu --</option>
                                @foreach($products as $jeu)
                                    <option value="{{ $jeu->id }}">{{ $jeu->name }} ({{ number_format($jeu->price, 2, ',', ' ') }} &euro;)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="add_quantity" value="1" min="1" max="99" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pq-panel p-4">
                    <div class="mb-3">
                        <label class="form-label" for="user_id">Client</label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $order->user_id) == $u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="status">Statut</label>
                        <select id="status" name="status" class="form-select" required>
                            @foreach($statuses as $cle => $libelle)
                                <option value="{{ $cle }}" @selected(old('status', $order->status) === $cle)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="shipping_address">Adresse</label>
                        <input type="text" id="shipping_address" name="shipping_address"
                               value="{{ old('shipping_address', $order->shipping_address) }}" class="form-control">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label" for="shipping_city">Ville</label>
                            <input type="text" id="shipping_city" name="shipping_city"
                                   value="{{ old('shipping_city', $order->shipping_city) }}" class="form-control">
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="shipping_zip_code">CP</label>
                            <input type="text" id="shipping_zip_code" name="shipping_zip_code"
                                   value="{{ old('shipping_zip_code', $order->shipping_zip_code) }}" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-pq w-100">Enregistrer</button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-link w-100 mt-2">Annuler</a>
                </div>
            </div>
        </div>
    </form>
@endsection
