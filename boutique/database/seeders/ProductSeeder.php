<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Remplit la table products ET la table pivot category_product.
 *
 * Les titres de jeux sont inventes et les jaquettes sont des images
 * generees automatiquement : j'ai prefere eviter de vrais jeux a cause
 * des droits d'auteur.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // pluck('id', 'name') fabrique un tableau ['Action' => 1, 'RPG' => 3...]
        // Comme ca je peux ecrire mes jeux avec des noms de categories
        // lisibles plus bas, et retrouver l'id au moment de les rattacher.
        $cat = Category::pluck('id', 'name');

        $products = [
            // Chaque ligne suit le meme ordre :
            // [nom, prix, stock, plateforme, editeur, pegi, vedette, categories, description]
            ['Nebula Drift', 59.99, 25, 'PlayStation 5', 'Orion Studios', 12, true,
             ['Action', 'Aventure'],
             "Explorez une ceinture d'asteroides a bord d'un vaisseau modulaire et affrontez les pirates de la Nebuleuse dans des combats spatiaux nerveux."],

            ['Chroniques d\'Aldoria', 69.99, 18, 'PC', 'Mythril Games', 16, true,
             ['RPG', 'Aventure'],
             "Un RPG de plus de 80 heures : trois royaumes, un systeme de classes libre et des choix moraux qui changent reellement la fin du jeu."],

            ['Velocity Redline', 49.99, 32, 'Xbox Series X', 'Apex Interactive', 3, false,
             ['Course', 'Sport'],
             "120 vehicules officiels, 40 circuits et un mode carriere complet. Conduite arcade accessible ou simulation exigeante, a vous de choisir."],

            ['Iron Protocol', 54.99, 12, 'PC', 'Blackline Softworks', 18, true,
             ['FPS', 'Action'],
             "Un FPS tactique ou chaque balle compte. Campagne cooperative a 4 et mode competitif classe en 5 contre 5."],

            ['Le Jardin des Lanternes', 24.99, 40, 'Nintendo Switch', 'Papier Lune', 7, false,
             ['Indie', 'Aventure'],
             "Un conte contemplatif peint a l'aquarelle. Ramenez la lumiere dans un village endormi en resolvant de douces enigmes."],

            ['Pixel Knights', 19.99, 55, 'Nintendo Switch', 'Papier Lune', 7, false,
             ['Plateforme', 'Indie'],
             "Retro-plateforme exigeant en pixel art : 60 niveaux, 8 boss et un mode speedrun avec classement en ligne."],

            ['Empire de Cendres', 44.99, 9, 'PC', 'Cartograph', 12, false,
             ['Strategie'],
             "4X au tour par tour : diplomatie, gestion des ressources et batailles rangees sur des cartes generees aleatoirement."],

            ['Asile 13', 39.99, 15, 'PlayStation 5', 'Nocturne Games', 18, false,
             ['Horreur', 'Aventure'],
             "Survival horror a la premiere personne. Pas d'arme, juste une lampe torche, un carnet et beaucoup trop de couloirs."],

            ['Grand Slam Tennis 26', 59.99, 22, 'Xbox Series X', 'Apex Interactive', 3, false,
             ['Sport'],
             "Mode carriere complet, 40 joueurs sous licence et un systeme de frappe base sur le timing."],

            ['Ronin: Lame de Jade', 64.99, 7, 'PlayStation 5', 'Mythril Games', 18, true,
             ['Action', 'RPG'],
             "Japon feodal, combat au katana exigeant et monde semi-ouvert. Parez au bon moment ou mourez en trois coups."],

            ['Starforge Tactics', 34.99, 28, 'PC', 'Cartograph', 12, false,
             ['Strategie', 'RPG'],
             "Tactique au tour par tour facon escouade : 30 missions, permadeath optionnelle et arbre technologique profond."],

            ['Neon Runner 2088', 29.99, 36, 'PC', 'Orion Studios', 16, false,
             ['Action', 'Plateforme'],
             "Course-poursuite cyberpunk en parkour a la premiere personne, bande-son synthwave et niveaux chronometres."],

            ['Harvest Cove', 27.99, 44, 'Nintendo Switch', 'Papier Lune', 3, false,
             ['Indie'],
             "Simulation de ferme et de vie en bord de mer : cultivez, pechez, renovez le village et nouez des amities."],

            ['Deadzone Protocol', 49.99, 0, 'Xbox Series X', 'Blackline Softworks', 18, false,
             ['FPS', 'Horreur'],
             "Extraction shooter cooperatif : entrez dans la zone, remplissez votre sac, ressortez vivant. Ou perdez tout."],

            ['Arene des Titans', 39.99, 19, 'PlayStation 5', 'Apex Interactive', 12, false,
             ['Action', 'Sport'],
             "Combat en arene a 8 joueurs avec des geants mecaniques entierement personnalisables."],

            ['Voiles et Tempetes', 54.99, 14, 'PC', 'Cartograph', 12, false,
             ['Aventure', 'Strategie'],
             "Commandez une flotte au XVIIIe siecle : commerce, exploration de cartes marines et abordages en temps reel."],
        ];

        foreach ($products as [$name, $price, $stock, $platform, $editor, $pegi, $featured, $cats, $desc]) {
            $product = Product::create([
                'name'        => $name,
                'description' => $desc,
                'price'       => $price,
                'stock'       => $stock,
                'platform'    => $platform,
                'editor'      => $editor,
                'pegi'        => $pegi,
                'featured'    => $featured,
                'image'       => 'https://placehold.co/960x540/1c1c28/8b5cf6?text=' . urlencode($name),
            ]);

            // RELATION MANY TO MANY : je rattache le jeu a ses categories.
            // Je pars des noms (['Action', 'RPG']) et je les transforme
            // en ids ([1, 3]) grace au tableau $cat du debut.
            // sync() ecrit ensuite les lignes dans category_product.
            $product->categories()->sync(
                collect($cats)->map(fn ($c) => $cat[$c])->all()
            );
        }
    }
}
