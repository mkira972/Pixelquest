<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * LA PAGE PROFIL.
 *
 * Deux exigences du cahier des charges sont ici :
 *   - modification des informations personnelles  -> update()
 *   - suppression du compte                       -> destroy()
 *
 * Le changement de mot de passe, lui, est gere par Breeze dans
 * Auth\PasswordController.
 */
class ProfileController extends Controller
{
    /**
     * Page profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user'   => $request->user(),
            'orders' => $request->user()->orders()->take(5)->get(),
        ]);
    }

    /**
     * Modification des informations personnelles.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            // unique()->ignore() : l'email doit etre unique en base, MAIS
            // il faut s'ignorer soi-meme. Sinon l'utilisateur ne pourrait
            // plus enregistrer son profil sans changer son propre email.
            'email'    => ['required', 'string', 'email', 'max:255',
                            Rule::unique('users')->ignore($request->user()->id)],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'phone'    => ['nullable', 'string', 'max:30'],
        ]);

        $user = $request->user();
        $user->fill($data);

        // isDirty() = "est-ce que ce champ a change depuis la lecture en base ?"
        // Si l'email change, on remet la verification a zero.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Informations mises a jour.');
    }

    /**
     * Suppression du compte (avec confirmation du mot de passe).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // current_password est une regle fournie par Laravel : elle
        // compare ce qui est saisi au mot de passe hache en base.
        // C'est ce qui empeche quelqu'un de supprimer le compte depuis
        // une session laissee ouverte sur un PC.
        //
        // validateWithBag met les erreurs dans un "sac" nomme, ce qui me
        // permet de les afficher dans la modale et pas sur les autres
        // formulaires de la page.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // On deconnecte AVANT de supprimer, sinon Laravel travaille
        // avec un utilisateur qui n'existe plus.
        Auth::logout();
        $user->delete();

        // On detruit la session et on regenere le jeton CSRF, pour qu'il
        // ne reste aucune trace exploitable de l'ancienne connexion.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Votre compte a bien ete supprime.');
    }
}
