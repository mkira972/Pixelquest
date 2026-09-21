<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GESTION DES COMMANDES, COTE ADMIN.
 *
 * Pas de create() ni de store() : une commande nait forcement d'un
 * client qui valide son panier. L'admin peut seulement les consulter,
 * les modifier et les supprimer.
 */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('user');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.orders.index', [
            'orders'   => $query->latest()->paginate(15)->withQueryString(),
            'statuses' => Order::STATUSES,
            'current'  => $status,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order'    => $order->load('user', 'products'),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function edit(Order $order): View
    {
        return view('admin.orders.edit', [
            'order'    => $order->load('user', 'products'),
            'statuses' => Order::STATUSES,
            'users'    => User::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'status'            => ['required', 'in:' . implode(',', array_keys(Order::STATUSES))],
            'shipping_address'  => ['nullable', 'string', 'max:255'],
            'shipping_city'     => ['nullable', 'string', 'max:100'],
            'shipping_zip_code' => ['nullable', 'string', 'max:20'],
            'lignes'            => ['nullable', 'array'],
            'lignes.*.quantity' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $order->update([
            'user_id'           => $data['user_id'],
            'status'            => $data['status'],
            'shipping_address'  => $data['shipping_address'] ?? null,
            'shipping_city'     => $data['shipping_city'] ?? null,
            'shipping_zip_code' => $data['shipping_zip_code'] ?? null,
        ]);

        // MISE A JOUR DES LIGNES DE COMMANDE (la table pivot).
        //
        // Ici je n'utilise pas sync() comme pour les categories, parce que
        // sync() ecraserait quantity et price. J'ai besoin de modifier une
        // ligne existante sans toucher au prix fige, d'ou updateExistingPivot().
        //
        // Une quantite mise a 0 veut dire "retire ce jeu de la commande".
        foreach ($request->input('lignes', []) as $productId => $ligne) {
            $qty = (int) ($ligne['quantity'] ?? 0);
            if ($qty <= 0) {
                $order->products()->detach($productId);
            } else {
                $order->products()->updateExistingPivot($productId, ['quantity' => $qty]);
            }
        }

        // AJOUT D'UN JEU a la commande. Je verifie qu'il n'y est pas deja,
        // sinon attach() creerait une deuxieme ligne pour le meme jeu.
        if ($request->filled('add_product_id')) {
            $product = Product::find($request->input('add_product_id'));
            if ($product && ! $order->products()->where('products.id', $product->id)->exists()) {
                $order->products()->attach($product->id, [
                    'quantity' => max(1, (int) $request->input('add_quantity', 1)),
                    'price'    => $product->price,
                ]);
            }
        }

        // RECALCUL DU TOTAL a partir des lignes du pivot.
        // refresh() relit la commande en base pour repartir des vraies
        // donnees, load('products') recharge les lignes modifiees juste
        // au-dessus. Sans ces deux appels je calculerais sur l'ancien etat.
        $order->refresh()->load('products');
        $order->update([
            'total' => $order->products->sum(fn ($p) => $p->pivot->price * $p->pivot->quantity),
        ]);

        return redirect()->route('admin.orders.show', $order)
                         ->with('success', "Commande {$order->reference} mise a jour.");
    }

    public function destroy(Order $order): RedirectResponse
    {
        $ref = $order->reference;
        $order->products()->detach();
        $order->delete();

        return redirect()->route('admin.orders.index')
                         ->with('success', "Commande {$ref} supprimee.");
    }
}
