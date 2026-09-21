<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des commandes.
 *
 * Ici le lien avec users est un One to Many tout simple : une commande
 * appartient a UN seul client, donc une colonne user_id suffit. Pas besoin
 * de table pivot comme pour les jeux.
 *
 * Les jeux de la commande, eux, sont dans order_product.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Le lien avec le compte client. Si le compte est supprime,
            // ses commandes sont supprimees aussi.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Numero de commande lisible, genre CMD-20260920-A3F9C1
            $table->string('reference', 30)->unique();

            // Recalcule a partir des lignes de order_product
            $table->decimal('total', 10, 2)->default(0);

            // enum = liste fermee de valeurs autorisees. MySQL refusera
            // tout ce qui n'est pas dans cette liste.
            $table->enum('status', ['en_attente', 'payee', 'expediee', 'livree', 'annulee'])
                  ->default('en_attente');

            // L'adresse est copiee dans la commande, pas juste liee au compte.
            // Comme ca si le client demenage, ses anciennes commandes gardent
            // l'adresse ou elles ont vraiment ete livrees.
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_zip_code', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
