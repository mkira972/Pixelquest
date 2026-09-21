<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEUXIEME RELATION MANY TO MANY : les commandes et les jeux.
 *
 * Celle-ci est un peu differente de category_product : en plus des deux
 * cles, elle stocke des informations propres au lien lui-meme.
 *
 * - quantity : combien d'exemplaires de ce jeu dans cette commande
 * - price    : le prix du jeu AU MOMENT DE L'ACHAT
 *
 * Le prix est recopie expres. Si demain je baisse le prix du jeu dans
 * l'admin, les commandes deja passees doivent garder leur ancien montant,
 * sinon le total affiche ne correspondrait plus a ce que le client a paye.
 *
 * C'est cette table qui represente les "lignes" d'une commande.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 8, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
