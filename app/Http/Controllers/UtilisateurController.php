<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gestion des comptes du personnel par l'administration.
 *
 * Les comptes étudiants ne sont pas créés ici : ils s'activent eux-mêmes
 * depuis l'annuaire ENEAM. Cette interface ne concerne que les rôles
 * `admin` et `bibliothecaire`.
 */
class UtilisateurController extends Controller
{
    public function index(): View
    {
        return view('utilisateurs.index', [
            'personnel' => User::query()
                ->whereIn('role', ['admin', 'bibliothecaire'])
                ->orderBy('role')
                ->orderBy('name')
                ->get(),
            'nbEtudiants' => User::query()->where('role', 'etudiant')->count(),
        ]);
    }

    public function create(): View
    {
        return view('utilisateurs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'bibliothecaire'])],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $utilisateur = DB::transaction(function () use ($donnees): User {
            $utilisateur = User::create([
                'name' => trim($donnees['name']),
                'email' => mb_strtolower(trim($donnees['email'])),
                'password' => $donnees['password'],
            ]);

            $utilisateur->forceFill([
                'role' => $donnees['role'],
                'actif' => true,
                'email_verified_at' => now(),
            ])->save();

            return $utilisateur;
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Le compte de '.$utilisateur->name.' a été créé.');
    }

    /**
     * Active ou désactive un compte du personnel.
     */
    public function toggle(Request $request, User $utilisateur): RedirectResponse
    {
        abort_unless(in_array($utilisateur->role, ['admin', 'bibliothecaire'], true), 404);

        if ((int) $utilisateur->id === (int) $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        if ($utilisateur->actif && $utilisateur->isAdmin()) {
            $autreAdminActif = User::query()
                ->where('role', 'admin')
                ->where('actif', true)
                ->whereKeyNot($utilisateur->id)
                ->exists();

            if (! $autreAdminActif) {
                return back()->with('error', 'Le dernier administrateur actif ne peut pas être désactivé.');
            }
        }

        $utilisateur->forceFill(['actif' => ! $utilisateur->actif])->save();

        return back()->with(
            'success',
            $utilisateur->actif
                ? 'Le compte de '.$utilisateur->name.' a été réactivé.'
                : 'Le compte de '.$utilisateur->name.' a été désactivé.'
        );
    }
}
