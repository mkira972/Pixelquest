<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute des colonnes a la table users, qui existait deja (elle vient
 * de Laravel Breeze avec name, email, password).
 *
 * Schema::table() = je modifie une table existante.
 * Schema::create() = j'en cree une nouvelle.
 *
 * La colonne importante ici c'est is_admin : c'est elle qui fait la
 * difference entre un client et un administrateur. Le middleware IsAdmin
 * ne regarde que ca.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // boolean = 0 ou 1. Par defaut false, donc un nouveau compte
            // est toujours un simple client, jamais un admin.
            $table->boolean('is_admin')->default(false)->after('password');

            // Adresse par defaut du client, pre-remplie au moment de commander
            $table->string('address')->nullable()->after('is_admin');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('zip_code', 20)->nullable()->after('city');
            $table->string('phone', 30)->nullable()->after('zip_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'address', 'city', 'zip_code', 'phone']);
        });
    }
};
