<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Un seul point d'entrée, trois tableaux de bord selon le rôle.
     */
    public function index(): View
    {
        $utilisateur = auth()->user();

        return match ($utilisateur->role) {
            'admin' => $this->tableauDeBordAdmin(),
            'bibliothecaire' => $this->tableauDeBordBibliothecaire(),
            default => $this->tableauDeBordEtudiant($utilisateur),
        };
    }

    private function tableauDeBordAdmin(): View
    {
        $dernieres = Memoire::with(['filiere', 'annee', 'etudiant'])
            ->orderByDesc('date_depot')
            ->limit(6)
            ->get();

        $repartition = Memoire::query()
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return view('dashboard.admin', [
            'nbFilieres'   => Filiere::count(),
            'nbAnnees'     => AnneeAcademique::count(),
            'nbMemoires'   => Memoire::count(),
            'nbEnAttente'  => (int) ($repartition['en_verification'] ?? 0),
            'repartition'  => $repartition,
            'dernieres'    => $dernieres,
        ]);
    }

    private function tableauDeBordBibliothecaire(): View
    {
        $file = Memoire::with(['filiere', 'annee', 'etudiant'])
            ->where('statut', 'en_verification')
            ->orderBy('date_depot')
            ->limit(8)
            ->get();

        return view('dashboard.bibliothecaire', [
            'nbEnAttente' => Memoire::where('statut', 'en_verification')->count(),
            'nbValides'   => Memoire::where('statut', 'valide')->count(),
            'nbRejetes'   => Memoire::where('statut', 'rejete')->count(),
            'nbTotal'     => Memoire::count(),
            'file'        => $file,
        ]);
    }

    private function tableauDeBordEtudiant($utilisateur): View
    {
        $requete = Memoire::where('etudiant_id', $utilisateur->id);

        return view('dashboard.etudiant', [
            'nbDepots'    => (clone $requete)->count(),
            'nbValides'   => (clone $requete)->where('statut', 'valide')->count(),
            'nbEnAttente' => (clone $requete)->whereIn('statut', ['soumis', 'en_verification'])->count(),
            'nbRejetes'   => (clone $requete)->where('statut', 'rejete')->count(),
            'derniers'    => $requete->with(['filiere', 'annee'])
                ->orderByDesc('date_depot')
                ->limit(5)
                ->get(),
        ]);
    }
}
