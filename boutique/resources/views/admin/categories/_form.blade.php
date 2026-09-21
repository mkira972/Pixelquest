@csrf
@if($category->exists) @method('PUT') @endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="pq-panel p-4">
            <div class="mb-3">
                <label class="form-label" for="name">Nom *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="slug">Slug (auto si vide)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                       class="form-control @error('slug') is-invalid @enderror">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="5"
                          class="form-control">{{ old('description', $category->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-pq w-100">
                {{ $category->exists ? 'Enregistrer les modifications' : 'Creer la categorie' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link w-100 mt-2">Annuler</a>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="pq-panel p-4">
            {{--
                LA PARTIE "modifier les articles d'une categorie".

                Exactement le meme principe que dans le formulaire d'un
                jeu, mais dans l'autre sens : ici je pars de la categorie
                et je coche les jeux. C'est la MEME table pivot
                category_product qui est ecrite au final.
            --}}
            <h2 class="h6 text-muted text-uppercase mb-1">Jeux de cette categorie</h2>

            <input type="text" class="form-control mb-3" id="filtre-jeux" placeholder="Rechercher un jeu...">

            <div class="row g-1" style="max-height: 420px; overflow-y: auto" id="liste-jeux">
                @foreach($products as $jeu)
                    <div class="col-md-6 jeu-item">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[]"
                                   value="{{ $jeu->id }}" id="jeu-{{ $jeu->id }}"
                                   @checked(in_array($jeu->id, old('products', $category->products->pluck('id')->all())))>
                            <label class="form-check-label" for="jeu-{{ $jeu->id }}">{{ $jeu->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Petit filtre en JavaScript : masque les jeux qui ne correspondent
     pas a ce qui est tape, sans recharger la page. --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const champ = document.getElementById('filtre-jeux');
        champ?.addEventListener('input', () => {
            const terme = champ.value.toLowerCase();
            document.querySelectorAll('#liste-jeux .jeu-item').forEach((el) => {
                el.style.display = el.textContent.toLowerCase().includes(terme) ? '' : 'none';
            });
        });
    });
</script>
