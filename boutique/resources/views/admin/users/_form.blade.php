@csrf
@if($user->exists) @method('PUT') @endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="pq-panel p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Nom *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="email">E-mail *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="password">
                        Mot de passe {{ $user->exists ? '(laisser vide pour ne pas changer)' : '*' }}
                    </label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           @required(! $user->exists)>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Confirmation</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" @required(! $user->exists)>
                </div>

                <div class="col-12">
                    <label class="form-label" for="address">Adresse</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}"
                           class="form-control">
                </div>

                <div class="col-md-5">
                    <label class="form-label" for="city">Ville</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="zip_code">Code postal</label>
                    <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="phone">Telephone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="pq-panel p-4">
            <h2 class="h6 text-muted text-uppercase mb-3">Role</h2>

            @if($user->exists && $user->id === auth()->id())
            @else
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1"
                           @checked(old('is_admin', $user->is_admin))>
                    <label class="form-check-label" for="is_admin">Compte administrateur</label>
                </div>
            @endif

            <button type="submit" class="btn btn-pq w-100 mt-3">
                {{ $user->exists ? 'Enregistrer les modifications' : "Creer l'utilisateur" }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link w-100 mt-2">Annuler</a>
        </div>
    </div>
</div>
