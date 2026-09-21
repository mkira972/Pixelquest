<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Action',        "Des jeux nerveux ou les reflexes font la difference."],
            ['Aventure',      "De grands voyages, des enigmes et des histoires marquantes."],
            ['RPG',           "Jeux de role : progression, quetes et personnalisation."],
            ['FPS',           "Jeux de tir a la premiere personne, solo et multijoueur."],
            ['Course',        "Pilotage arcade ou simulation, sur asphalte ou en tout-terrain."],
            ['Sport',         "Football, basket, glisse : toutes les disciplines."],
            ['Strategie',     "Tour par tour ou temps reel, pour les fins tacticiens."],
            ['Indie',         "Pepites independantes, direction artistique originale."],
            ['Horreur',       "Survival horror et ambiances angoissantes."],
            ['Plateforme',    "Sauts millimetres et niveaux ingenieux."],
        ];

        foreach ($categories as [$name, $description]) {
            Category::create([
                'name'        => $name,
                'description' => $description,
            ]);
        }
    }
}
