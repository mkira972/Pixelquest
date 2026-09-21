<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();

            // foreignId + constrained : cree la colonne ET la cle etrangere.
            // onDelete('cascade') : si je supprime une categorie, les lignes
            // de cette table qui la concernent partent avec elle. Sinon je me
            // retrouverais avec des liens vers une categorie qui n'existe plus.
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Empeche d'enregistrer deux fois le meme jeu dans la meme categorie
            $table->unique(['category_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
