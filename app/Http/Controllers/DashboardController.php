<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
use App\Models\Validation;
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

        $repartition = $this->repartitionParStatut();

        return view('dashboard.admin', [
            'nbFilieres'   => Filiere::count(),
            'nbAnnees'     => AnneeAcademique::count(),
            'nbMemoires'   => Memoire::count(),
            'nbEnAttente'  => (int) ($repartition['en_verification'] ?? 0),
            'nbACorriger'  => (int) ($repartition['a_corriger'] ?? 0),
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

        $repartition = $this->repartitionParStatut();

        return view('dashboard.bibliothecaire', [
            'nbTotal'     => Memoire::count(),
            'nbEnAttente' => (int) ($repartition['en_verification'] ?? 0),
            'nbACorriger' => (int) ($repartition['a_corriger'] ?? 0),
            'nbValides'   => (int) ($repartition['valide'] ?? 0),
            'nbRejetes'   => (int) ($repartition['rejete'] ?? 0),
            'nbEtudiants' => User::where('role', 'etudiant')->where('actif', true)->count(),
            'repartition' => $repartition,
            'file'        => $file,
            'activites'   => $this->activitesRecentes(),
        ]);
    }

    private function tableauDeBordEtudiant(User $utilisateur): View
    {
        $requete = Memoire::where('etudiant_id', $utilisateur->id);

        return view('dashboard.etudiant', [
            'nbDepots'    => (clone $requete)->count(),
            'nbValides'   => (clone $requete)->where('statut', 'valide')->count(),
            'nbEnAttente' => (clone $requete)->whereIn('statut', ['soumis', 'en_verification'])->count(),
            'nbACorriger' => (clone $requete)->where('statut', 'a_corriger')->count(),
            'nbRejetes'   => (clone $requete)->where('statut', 'rejete')->count(),
            'derniers'    => $requete->with(['filiere', 'annee'])
                ->orderByDesc('date_depot')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * Comptage des mémoires par statut, réutilisé par les tableaux de bord.
     */
    private function repartitionParStatut()
    {
        return Memoire::query()
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');
    }

    /**
     * Flux d'activité : dernières décisions de validation + derniers dépôts.
     */
    private function activitesRecentes()
    {
        $decisions = Validation::with(['memoire'])
            ->whereNotNull('date_decision')
            ->orderByDesc('date_decision')
            ->limit(5)
            ->get()
            ->map(fn (Validation $validation) => [
                'type' => 'validation',
                'titre' => $validation->memoire?->titre ?? 'Mémoire supprimé',
                'sous_titre' => match ($validation->statut) {
                    'valide' => 'Mémoire validé',
                    'rejete' => 'Mémoire rejeté',
                    'a_corriger' => 'Correction demandée',
                    default => 'Vérification en cours',
                },
                'ton' => match ($validation->statut) {
                    'valide' => 'emerald',
                    'rejete' => 'rose',
                    'a_corriger' => 'orange',
                    default => 'amber',
                },
                'date' => $validation->date_decision,
            ]);

        $depots = Memoire::with(['etudiant'])
            ->orderByDesc('date_depot')
            ->limit(5)
            ->get()
            ->map(fn (Memoire $memoire) => [
                'type' => 'depot',
                'titre' => $memoire->titre,
                'sous_titre' => 'Nouveau dépôt — '.($memoire->etudiant?->name ?? $memoire->auteur_nom ?? 'Auteur inconnu'),
                'ton' => 'indigo',
                'date' => $memoire->date_depot,
            ]);

        return $decisions->concat($depots)
            ->sortByDesc('date')
            ->take(6)
            ->values();
    }
}
