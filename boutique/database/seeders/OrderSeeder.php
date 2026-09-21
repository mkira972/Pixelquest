<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Cree des commandes au hasard pour que l'admin ne soit pas vide
 * au premier lancement.
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::where('is_admin', false)->get();
        $products = Product::where('stock', '>', 0)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuts = array_keys(Order::STATUSES);

        foreach ($users as $index => $user) {
            $nbCommandes = rand(1, 3);

            for ($i = 0; $i < $nbCommandes; $i++) {
                $order = Order::create([
                    'user_id'           => $user->id,
                    'reference'         => Order::generateReference() . '-' . $index . $i,
                    'total'             => 0,
                    'status'            => $statuts[array_rand($statuts)],
                    'shipping_address'  => $user->address ?: rand(1, 80) . ' rue des Developpeurs',
                    'shipping_city'     => $user->city ?: 'Cayenne',
                    'shipping_zip_code' => $user->zip_code ?: '97300',
                ]);

                // Je force la date de creation pour avoir des commandes
                // etalees dans le temps. forceFill contourne la protection
                // du $fillable, qui bloque normalement created_at.
                $date = now()->subDays(rand(1, 60));
                $order->forceFill(['created_at' => $date, 'updated_at' => $date])->save();

                $total = 0;

                // 1 a 3 jeux differents par commande, pris au hasard.
                foreach ($products->random(rand(1, 3)) as $product) {
                    $qty = rand(1, 2);

                    // attach() ecrit une ligne dans le pivot order_product,
                    // avec la quantite et le prix, exactement comme le fait
                    // OrderController quand un vrai client commande.
                    $order->products()->attach($product->id, [
                        'quantity' => $qty,
                        'price'    => $product->price,
                    ]);

                    $total += $product->price * $qty;
                }

                $order->update(['total' => $total]);
            }
        }
    }
}
