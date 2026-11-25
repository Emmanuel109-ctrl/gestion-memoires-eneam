<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Page d'accueil publique : présentation, recherche et mémoires récents.
     */
    public function index(Request $request): View
    {
        $utilisateur = $request->user();

        $base = Memoire::query()
            ->visiblesPour($utilisateur)
            ->where('statut', 'valide');

        $recents = (clone $base)
            ->with(['filiere', 'annee'])
            ->orderByDesc('date_depot')
            ->limit(6)
            ->get();

        return view('accueil', [
            'recents' => $recents,
            'nbMemoires' => (clone $base)->count(),
            'nbFilieres' => (clone $base)->distinct()->count('filiere_id'),
            'nbAnnees' => (clone $base)->distinct()->count('annee_id'),
            'nbAuteurs' => (clone $base)->whereNotNull('auteur_nom')->distinct()->count('auteur_nom'),
            'filieres' => Filiere::orderBy('nom')->get(),
            'dernieresAnnees' => AnneeAcademique::orderByDesc('date_debut')->limit(4)->get(),
        ]);
    }
}
