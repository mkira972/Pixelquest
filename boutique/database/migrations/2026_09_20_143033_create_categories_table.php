<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table des categories de jeux (Action, RPG, FPS...).
 *
 * Une migration, c'est la structure d'une table ecrite en PHP au lieu de SQL.
 * L'interet : je peux recreer toute la base sur n'importe quel PC avec une
 * seule commande, et le fichier part dans Git comme le reste du code.
 */
return new class extends Migration
{
    // up() = ce qui se passe quand je lance la migration
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                               // cle primaire auto-incrementee
            $table->string('name');                     // "Action", "RPG"...
            $table->string('slug')->unique();           // version URL du nom : "jeux-de-role"
            $table->text('description')->nullable();    // nullable = le champ peut rester vide
            $table->timestamps();                       // cree created_at et updated_at
        });
    }

    // down() = l'inverse, pour annuler la migration
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
