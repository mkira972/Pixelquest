<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des jeux vendus dans la boutique.
 *
 * Attention : il n'y a PAS de colonne category_id ici. Un jeu peut appartenir
 * a plusieurs categories a la fois (Action ET RPG par exemple), donc le lien
 * est fait dans une table separee : category_product.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            $table->text('description')->nullable();

            // decimal(8,2) = 8 chiffres en tout dont 2 apres la virgule.
            // Je n'utilise pas float : avec des prix ca fait des erreurs d'arrondi.
            $table->decimal('price', 8, 2);

            // unsigned = jamais negatif, un stock ne peut pas etre en dessous de 0
            $table->unsignedInteger('stock')->default(0);

            $table->string('image')->nullable();        // URL de la jaquette
            $table->string('platform', 60)->nullable(); // PC, PS5, Switch...
            $table->string('editor', 100)->nullable();
            $table->unsignedTinyInteger('pegi')->nullable(); // 3, 7, 12, 16 ou 18

            // Sert a choisir les jeux affiches en avant sur la page d'accueil
            $table->boolean('featured')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
