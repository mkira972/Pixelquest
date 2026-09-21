<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Une commande passee par un client.
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reference', 'total', 'status',
        'shipping_address', 'shipping_city', 'shipping_zip_code',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /**
     * La liste des statuts possibles, au meme endroit pour tout le projet.
     * A gauche ce qui est stocke en base, a droite ce qui s'affiche a l'ecran.
     *
     * L'interet de la constante : si je veux ajouter un statut, je le fais
     * ici une seule fois et il apparait partout (formulaire admin, filtre,
     * badge de couleur). Je ne recopie pas la liste dans chaque vue.
     */
    public const STATUSES = [
        'en_attente' => 'En attente',
        'payee'      => 'Payee',
        'expediee'   => 'Expediee',
        'livree'     => 'Livree',
        'annulee'    => 'Annulee',
    ];

    /**
     * Relation ONE TO MANY dans l'autre sens : une commande appartient
     * a un seul client. Ca me permet d'ecrire $order->user->name.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION MANY TO MANY avec les jeux, via order_product.
     * withPivot rend lisibles la quantite et le prix de chaque ligne.
     *
     * Dans une vue j'ecris :
     *   $product->pivot->quantity  -> combien d'exemplaires
     *   $product->pivot->price     -> le prix paye ce jour-la
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    /**
     * Transforme "en_attente" en "En attente" pour l'affichage.
     * Utilisable avec $order->status_label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Donne la couleur Bootstrap du badge selon le statut.
     * match() est un switch en plus court (PHP 8).
     * Utilisable avec $order->status_color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'en_attente' => 'warning',
            'payee'      => 'info',
            'expediee'   => 'primary',
            'livree'     => 'success',
            'annulee'    => 'danger',
            default      => 'secondary',
        };
    }

    /**
     * Additionne les quantites de toutes les lignes du pivot.
     * Une commande avec 2 exemplaires d'un jeu et 1 d'un autre
     * renvoie 3, et pas 2 lignes.
     */
    public function getTotalItemsAttribute(): int
    {
        return (int) $this->products->sum('pivot.quantity');
    }

    /**
     * Fabrique un numero de commande du style CMD-20260920-A3F9C1.
     * La partie aleatoire evite que deux commandes passees le meme
     * jour aient la meme reference.
     */
    public static function generateReference(): string
    {
        return 'CMD-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
