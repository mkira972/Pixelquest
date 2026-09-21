<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Un jeu video de la boutique.
 *
 * Un modele Eloquent represente UNE table. Par defaut Laravel devine
 * le nom de la table en mettant le nom de la classe au pluriel :
 * Product -> products. Du coup je n'ai rien a preciser.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Les champs qu'on a le droit de remplir d'un coup avec create()
     * ou update(). C'est une securite : si quelqu'un bidouille le
     * formulaire et envoie un champ "id" ou "is_admin", Laravel l'ignore
     * parce qu'il n'est pas dans cette liste.
     */
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock',
        'image', 'platform', 'editor', 'pegi', 'featured',
    ];

    /**
     * Convertit automatiquement les valeurs quand je les lis.
     * En base, MySQL me rend "59.99" sous forme de texte et 1 pour un
     * booleen. Avec ca je recupere directement un nombre et un vrai
     * true/false en PHP.
     */
    protected $casts = [
        'price'    => 'decimal:2',
        'featured' => 'boolean',
    ];

    /**
     * booted() se lance automatiquement au demarrage du modele.
     * Ici je branche un ecouteur sur l'evenement "saving", qui se
     * declenche juste avant chaque enregistrement.
     *
     * Resultat : si l'admin laisse le champ slug vide, il se fabrique
     * tout seul a partir du nom. "Nebula Drift" devient "nebula-drift".
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * RELATION MANY TO MANY avec les categories.
     *
     * belongsToMany dit a Laravel : "va chercher dans la table pivot".
     * Il devine tout seul qu'elle s'appelle category_product.
     *
     * Concretement je peux ecrire $product->categories et recuperer
     * la liste des categories du jeu, sans ecrire une seule requete SQL.
     *
     * withTimestamps() met a jour created_at / updated_at dans le pivot.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    /**
     * RELATION MANY TO MANY avec les commandes.
     *
     * Meme principe, sauf que le pivot order_product contient des
     * colonnes en plus. withPivot() sert a les rendre lisibles :
     * sans cette ligne, $product->pivot->quantity renverrait null.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    /**
     * Accesseur : une propriete calculee qui n'existe pas en base.
     * Grace au nom getImageUrlAttribute, j'ecris simplement
     * $product->image_url dans mes vues.
     *
     * Trois cas possibles :
     *   1. la colonne est vide          -> image de remplacement en ligne
     *   2. elle contient une URL        -> je la renvoie telle quelle
     *      (c'est le cas des jeux crees par le seeder)
     *   3. elle contient un chemin      -> fichier envoye depuis l'admin,
     *      genre "uploads/products/nebula-drift-a1b2c3.jpg".
     *      asset() le transforme en URL complete.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return 'https://placehold.co/960x540/1c1c28/8b5cf6?text=' . urlencode($this->name);
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset($this->image);
    }

    /**
     * Petite methode pratique pour eviter d'ecrire $product->stock > 0
     * un peu partout dans les vues.
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
}
