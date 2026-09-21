<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Le point d'entree des donnees de test.
     * Lance par : php artisan migrate:fresh --seed
     *
     * L'ORDRE EST IMPORTANT. Chaque seeder a besoin du precedent :
     * les jeux doivent etre rattaches a des categories qui existent
     * deja, et les commandes a des clients et des jeux qui existent.
     * Si j'inversais deux lignes, ca planterait.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
