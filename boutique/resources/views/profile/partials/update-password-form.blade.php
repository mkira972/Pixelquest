<div class="pq-panel p-4 mb-4">
    <h2 class="h5 mb-1">Mot de passe</h2>
    @if(session('status') === 'password-updated')
        <div class="alert alert-success">Mot de passe mis a jour.</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password"
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="update_password">Nouveau mot de passe</label>
                <input type="password" id="update_password" name="password"
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="update_password_confirmation">Confirmation</label>
                <input type="password" id="update_password_confirmation" name="password_confirmation"
                       class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-pq mt-3">Changer le mot de passe</button>
    </form>
</div>
