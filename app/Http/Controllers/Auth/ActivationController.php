<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ActivationRequest;
use App\Models\EtudiantEneam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Activation du compte étudiant à partir de l'annuaire ENEAM.
 *
 * Ce n'est pas une inscription publique : seuls les étudiants déjà
 * présents dans l'annuaire, avec l'e-mail correspondant, peuvent activer
 * leur compte.
 */
class ActivationController extends Controller
{
    public function create(): View
    {
        return view('auth.premiere-connexion');
    }

    public function store(ActivationRequest $request): RedirectResponse
    {
        $matricule = $request->string('matricule')->toString();
        $email = mb_strtolower($request->string('email')->toString());

        $etudiant = EtudiantEneam::query()
            ->where('matricule', $matricule)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        // Message générique : on ne révèle pas si le matricule existe.
        if ($etudiant === null || ! $etudiant->actif) {
            return back()
                ->withInput($request->only('matricule', 'email'))
                ->withErrors([
                    'matricule' => "Ces informations ne correspondent pas à l'annuaire ENEAM. Contactez la bibliothèque.",
                ]);
        }

        $compteExistant = User::query()
            ->where('matricule', $matricule)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($compteExistant) {
            return redirect()->route('login')->withErrors([
                'identifiant' => 'Un compte est déjà associé à ces informations. Connectez-vous.',
            ]);
        }

        $utilisateur = DB::transaction(function () use ($request, $etudiant): User {
            $utilisateur = User::create([
                'name' => $etudiant->nom,
                'email' => $etudiant->email,
                'password' => $request->string('password')->toString(),
                'matricule' => $etudiant->matricule,
            ]);

            $utilisateur->forceFill([
                'role' => 'etudiant',
                'actif' => true,
                'email_verified_at' => now(),
            ])->save();

            return $utilisateur;
        });

        Auth::login($utilisateur);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Votre compte étudiant est activé. Le catalogue complet est désormais accessible.');
    }
}
