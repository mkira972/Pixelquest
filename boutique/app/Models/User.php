<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Un compte, client ou administrateur.
 *
 * Ce modele n'etend pas Model comme les autres mais Authenticatable,
 * parce que c'est lui qui sert a la connexion. C'est ce qui permet
 * a Laravel de faire Auth::user(), la verification du mot de passe, etc.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'address',
        'city',
        'zip_code',
        'phone',
    ];

    /**
     * Ces champs ne sortent jamais quand le modele est converti en JSON.
     * Ca evite d'envoyer le mot de passe hache par erreur.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * is_admin est stocke en 0/1 dans MySQL. Le cast me rend un vrai
     * booleen PHP, donc je peux ecrire if ($user->is_admin) sans risque.
     *
     * Remarque : je n'ai PAS mis 'password' => 'hashed' ici, alors que
     * Laravel 10 le propose. Les controleurs de Breeze appellent deja
     * Hash::make(), donc avec le cast en plus le mot de passe serait
     * hache deux fois et plus personne ne pourrait se connecter.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'          => 'boolean',
    ];

    /**
     * Relation ONE TO MANY : un client a plusieurs commandes.
     * latest() les trie de la plus recente a la plus ancienne,
     * comme ca je n'ai pas a le refaire dans chaque controleur.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Utilisee par le middleware IsAdmin.
     * Je passe par une methode plutot que de lire is_admin directement :
     * si un jour la regle change (des roles par exemple), je ne modifie
     * que cette ligne.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
