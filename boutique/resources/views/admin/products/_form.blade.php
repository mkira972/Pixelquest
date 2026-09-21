{{--
    Formulaire utilise A LA FOIS pour creer et pour modifier un jeu.
    C'est $product->exists qui fait la difference : false pour un
    nouveau jeu, true pour un jeu deja en base.

    @csrf ajoute un champ cache avec un jeton unique. Sans lui Laravel
    refuse le formulaire (erreur 419). Ca protege contre les attaques
    CSRF, ou un autre site ferait soumettre le formulaire a ma place.

    @method('PUT') : un navigateur ne sait envoyer que GET et POST.
    Cette directive ajoute un champ cache _method=PUT que Laravel lit
    pour router vers la methode update() au lieu de store().
--}}
@csrf
@if($product->exists) @method('PUT') @endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="pq-panel p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="name">Nom du jeu *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="slug">Slug (auto si vide)</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}"
                           class="form-control @error('slug') is-invalid @enderror">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="5"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="price">Prix (&euro;) *</label>
                    <input type="number" step="0.01" min="0" id="price" name="price"
                           value="{{ old('price', $product->price) }}"
                           class="form-control @error('price') is-invalid @enderror" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="stock">Stock *</label>
                    <input type="number" min="0" id="stock" name="stock"
                           value="{{ old('stock', $product->stock ?? 0) }}"
                           class="form-control @error('stock') is-invalid @enderror" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="pegi">PEGI</label>
                    <select id="pegi" name="pegi" class="form-select">
                        <option value="">-</option>
                        @foreach([3, 7, 12, 16, 18] as $p)
                            <option value="{{ $p }}" @selected(old('pegi', $product->pegi) == $p)>{{ $p }}+</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="platform">Plateforme</label>
                    <input type="text" id="platform" name="platform" list="plateformes"
                           value="{{ old('platform', $product->platform) }}" class="form-control">
                    <datalist id="plateformes">
                        <option value="PC"><option value="PlayStation 5"><option value="Xbox Series X">
                        <option value="Nintendo Switch">
                    </datalist>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="editor">Editeur</label>
                    <input type="text" id="editor" name="editor" value="{{ old('editor', $product->editor) }}"
                           class="form-control">
                </div>

                {{--
                    L'image de couverture : un vrai fichier envoye depuis
                    le PC. type="file" affiche le bouton « Parcourir »,
                    et accept limite ce que propose l'explorateur.
                --}}
                <div class="col-12">
                    <label class="form-label" for="image">Image de couverture</label>
                    <input type="file" id="image" name="image"
                           accept="image/jpeg,image/png,image/webp"
                           class="form-control @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- La case pour retirer l'image, seulement si le jeu en a
                     deja une qui vient d'un fichier envoye. --}}
                @if($product->exists && $product->image && ! Str::startsWith($product->image, ['http://', 'https://']))
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="supprimer_image" name="supprimer_image" value="1">
                            <label class="form-check-label" for="supprimer_image">
                                Supprimer l'image actuelle
                            </label>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                               @checked(old('featured', $product->featured))>
                        <label class="form-check-label" for="featured">Mettre en avant sur la page d'accueil</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="pq-panel p-4 mb-3">
            <h2 class="h6 text-muted text-uppercase mb-3">Categories du jeu</h2>

            {{--
                LA PARTIE "modifier les categories d'un article".

                name="categories[]" avec les crochets : le navigateur
                envoie toutes les cases cochees dans un seul tableau.
                Cote PHP je recois [1, 4, 7], que je passe directement
                a sync() dans le controleur.

                @checked() coche la case si la categorie est deja liee
                au jeu. old() sert a retrouver ce qui etait coche quand
                la validation echoue et qu'on revient sur le formulaire.
            --}}

            <div style="max-height: 320px; overflow-y: auto">
                @foreach($categories as $categorie)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="categories[]"
                               value="{{ $categorie->id }}" id="cat-{{ $categorie->id }}"
                               @checked(in_array($categorie->id, old('categories', $product->categories->pluck('id')->all())))>
                        <label class="form-check-label" for="cat-{{ $categorie->id }}">{{ $categorie->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pq-panel p-4">
            <h2 class="h6 text-muted text-uppercase mb-3">Apercu</h2>

            {{-- L'image actuelle, remplacee en direct par celle qu'on
                 vient de choisir grace au petit script plus bas. --}}
            <img src="{{ $product->image_url }}" id="apercu-image"
                 class="img-fluid rounded mb-3" alt="Apercu de la couverture">
            <button type="submit" class="btn btn-pq w-100">
                {{ $product->exists ? 'Enregistrer les modifications' : 'Creer le jeu' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-link w-100 mt-2">Annuler</a>
        </div>
    </div>
</div>


{{--
    Apercu instantane de l'image choisie, avant meme d'enregistrer.
    FileReader lit le fichier dans le navigateur et le transforme en
    donnees affichables. Rien n'est envoye au serveur a ce stade.
--}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const champ = document.getElementById('image');
        const apercu = document.getElementById('apercu-image');

        champ?.addEventListener('change', () => {
            const fichier = champ.files[0];
            if (!fichier) return;

            const lecteur = new FileReader();
            lecteur.onload = (e) => { apercu.src = e.target.result; };
            lecteur.readAsDataURL(fichier);
        });
    });
</script>
