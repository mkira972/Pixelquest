<div class="pq-panel p-4 mb-4">
    <h2 class="h5 mb-1">Informations personnelles</h2>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Nom</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="address">Adresse</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}"
                       class="form-control @error('address') is-invalid @enderror">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-5">
                <label class="form-label" for="city">Ville</label>
                <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}"
                       class="form-control @error('city') is-invalid @enderror">
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="zip_code">Code postal</label>
                <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}"
                       class="form-control @error('zip_code') is-invalid @enderror">
                @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="phone">Telephone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="form-control @error('phone') is-invalid @enderror">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn-pq mt-3">Enregistrer</button>
    </form>
</div>
