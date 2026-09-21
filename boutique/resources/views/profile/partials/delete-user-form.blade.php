<div class="pq-panel p-4 border-danger">
    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression">
        Supprimer mon compte
    </button>

    <div class="modal fade" id="modalSuppression" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header border-secondary">
                        <h3 class="modal-title h5">Confirmer la suppression</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label" for="delete_password">Mot de passe</label>
                        <input type="password" id="delete_password" name="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" required>
                        @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer definitivement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('modalSuppression')).show();
        });
    </script>
@endif
