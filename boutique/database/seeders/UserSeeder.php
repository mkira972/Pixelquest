<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cree les comptes de demonstration.
 *
 * Hash::make() transforme le mot de passe en empreinte illisible.
 * On ne stocke JAMAIS un mot de passe en clair en base : meme moi,
 * en regardant la table users, je ne dois pas pouvoir le lire.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Le compte administrateur : c'est is_admin => true qui lui
        // donne acces a tout l'espace /admin.
        User::create([
            'name'              => 'Administrateur',
            'email'             => 'admin@pixelquest.fr',
            'password'          => Hash::make('password'),
            'is_admin'          => true,
            'email_verified_at' => now(),
            'address'           => '1 rue des Pixels',
            'city'              => 'Cayenne',
            'zip_code'          => '97300',
            'phone'             => '0694000000',
        ]);

        User::create([
            'name'              => 'Client Demo',
            'email'             => 'client@pixelquest.fr',
            'password'          => Hash::make('password'),
            'is_admin'          => false,
            'email_verified_at' => now(),
            'address'           => '12 avenue du Joystick',
            'city'              => 'Matoury',
            'zip_code'          => '97351',
            'phone'             => '0694111111',
        ]);

        // Quelques clients en plus, pour que la liste des utilisateurs
        // et les statistiques de l'admin ne soient pas vides.
        $clients = [
            ['Lucas Moreau', 'lucas@example.com'],
            ['Emma Dubois', 'emma@example.com'],
            ['Nathan Leroy', 'nathan@example.com'],
            ['Chloe Bernard', 'chloe@example.com'],
            ['Hugo Petit', 'hugo@example.com'],
        ];

        foreach ($clients as [$name, $email]) {
            User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make('password'),
                'is_admin'          => false,
                'email_verified_at' => now(),
            ]);
        }
    }
}
