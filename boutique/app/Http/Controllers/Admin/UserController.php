<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * CRUD DES UTILISATEURS, COTE ADMIN.
 * C'est ici que l'admin peut creer, modifier ou supprimer des comptes,
 * et donner ou retirer les droits administrateur.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount('orders');

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return view('admin.users.index', [
            'users'  => $query->latest()->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'phone'    => ['nullable', 'string', 'max:30'],
        ]);

        // Le mot de passe est hache avant d'aller en base. Hash::make
        // utilise bcrypt, et le resultat est different a chaque fois
        // meme pour le meme mot de passe (grace au sel).
        $data['password'] = Hash::make($data['password']);
        $data['is_admin'] = $request->boolean('is_admin');

        $user = User::create($data);

        return redirect()->route('admin.users.index')
                         ->with('success', "L'utilisateur « {$user->name} » a ete cree.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'phone'    => ['nullable', 'string', 'max:30'],
        ]);

        // En modification le champ mot de passe est facultatif.
        // S'il est vide je le retire du tableau, sinon j'ecraserais
        // le mot de passe existant par une chaine vide hachee.
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // GARDE-FOU : un admin ne peut pas se retirer ses propres droits.
        // Sans ca, une fausse manip et plus personne ne peut entrer dans
        // l'espace admin. Il faudrait repasser par la base a la main.
        $data['is_admin'] = $user->id === $request->user()->id
            ? $user->is_admin
            : $request->boolean('is_admin');

        $user->update($data);

        return redirect()->route('admin.users.index')
                         ->with('success', "L'utilisateur « {$user->name} » a ete modifie.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Meme idee : interdit de supprimer son propre compte depuis
        // la liste. Pour ca il faut passer par la page profil, qui
        // redemande le mot de passe.
        if ($user->id === $request->user()->id) {
            return back()->with('error', "Vous ne pouvez pas supprimer votre propre compte ici.");
        }

        $nom = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', "L'utilisateur « {$nom} » a ete supprime.");
    }
}
