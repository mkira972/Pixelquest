<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Une categorie de jeux : Action, RPG, FPS, Horreur...
 * Correspond a la table categories.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Comme pour Product : le slug se genere tout seul si l'admin
     * ne le remplit pas.
     */
    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * L'AUTRE SENS de la relation Many to Many.
     *
     * Dans Product j'ai ecrit categories(), ici j'ecris products().
     * C'est exactement la meme table pivot category_product, lue dans
     * l'autre sens. C'est ce qui me permet, dans l'admin, de modifier
     * les categories d'un jeu OU les jeux d'une categorie, au choix.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    /**
     * Dit a Laravel d'utiliser l'id dans les URL plutot que le slug.
     * C'est ce qui fait que mes liens ressemblent a /categorie/3.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
