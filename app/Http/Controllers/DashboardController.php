<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\HistoriqueLecture;
use App\Models\Memoire;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $nbMemoires = Memoire::count();
        $nbValides = (int) ($repartition['valide'] ?? 0);

        // Usage réel du fonds : lectures et téléchargements journalisés.
        $nbConsultations = HistoriqueLecture::count();
        $nbTelechargements = HistoriqueLecture::where('type', 'telechargement')->count();

        $topConsultes = HistoriqueLecture::query()
            ->select('memoire_id', DB::raw('COUNT(*) as total'))
            ->with('memoire:id,titre,filiere_id')
            ->groupBy('memoire_id')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $tailleStockage = Memoire::whereNotNull('fichier_pdf')
            ->pluck('fichier_pdf')
            ->sum(function (string $chemin): int {
                $disque = Storage::disk('local');

                return $disque->exists($chemin) ? $disque->size($chemin) : 0;
            });

        return view('dashboard.admin', [
            'anneeCourante' => AnneeAcademique::where('active', true)->value('libelle'),
            'nbFilieres' => Filiere::count(),
            'nbAnnees' => AnneeAcademique::count(),
            'nbMemoires' => $nbMemoires,
            'nbEnAttente' => (int) (($repartition['soumis'] ?? 0) + ($repartition['en_verification'] ?? 0)),
            'nbValides' => $nbValides,
            'nbACorriger' => (int) ($repartition['a_corriger'] ?? 0),
            'nbUtilisateurs' => User::where('actif', true)->count(),
            'nbEtudiants' => User::where('role', 'etudiant')->where('actif', true)->count(),
            'repartition' => $repartition,
            'dernieres' => $dernieres,
            'activites' => $this->activitesRecentes(),
            'parFiliere' => Filiere::withCount('memoires')->orderByDesc('memoires_count')->take(5)->get(),
            'tauxValidation' => $nbMemoires > 0 ? (int) round($nbValides * 100 / $nbMemoires) : 0,
            'nbConsultations' => $nbConsultations,
            'nbTelechargements' => $nbTelechargements,
            'topConsultes' => $topConsultes,
            'tailleStockage' => $tailleStockage,
        ]);
    }

    private function tableauDeBordBibliothecaire(): View
    {
        $file = Memoire::with(['filiere', 'annee', 'etudiant'])
            ->whereIn('statut', ['soumis', 'en_verification'])
            ->orderBy('date_depot')
            ->limit(8)
            ->get();

        $repartition = $this->repartitionParStatut();

        $plusAncien = Memoire::whereIn('statut', ['soumis', 'en_verification'])->min('date_depot');
        $joursFile = $plusAncien !== null
            ? max(0, (int) \Illuminate\Support\Carbon::parse($plusAncien)->diffInDays(now()))
            : null;

        $decisionsSemaine = Validation::whereNotNull('date_decision')
            ->where('date_decision', '>=', now()->subDays(7))
            ->count();

        return view('dashboard.bibliothecaire', [
            'nbTotal' => Memoire::count(),
            'nbEnAttente' => Memoire::whereIn('statut', ['soumis', 'en_verification'])->count(),
            'nbACorriger' => (int) ($repartition['a_corriger'] ?? 0),
            'nbValides' => (int) ($repartition['valide'] ?? 0),
            'nbRejetes' => (int) ($repartition['rejete'] ?? 0),
            'nbEtudiants' => User::where('role', 'etudiant')->where('actif', true)->count(),
            'repartition' => $repartition,
            'file' => $file,
            'activites' => $this->activitesRecentes(),
            'joursFile' => $joursFile,
            'decisionsSemaine' => $decisionsSemaine,
        ]);
    }

    private function tableauDeBordEtudiant(User $utilisateur): View
    {
        $requete = Memoire::where('etudiant_id', $utilisateur->id);

        $consultations = HistoriqueLecture::with(['memoire.filiere'])
            ->where('user_id', $utilisateur->id)
            ->whereHas('memoire')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $dejaConsultes = $consultations->pluck('memoire_id');

        // Suggestions : derniers mémoires validés que l'étudiant n'a pas encore ouverts.
        $suggestions = Memoire::query()
            ->visiblesPour($utilisateur)
            ->with(['filiere', 'annee'])
            ->where('statut', 'valide')
            ->where(function ($q) use ($utilisateur): void {
                $q->whereNull('etudiant_id')->orWhere('etudiant_id', '!=', $utilisateur->id);
            })
            ->when($dejaConsultes->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $dejaConsultes))
            ->orderByDesc('date_depot')
            ->limit(3)
            ->get();

        return view('dashboard.etudiant', [
            'nbDepots' => (clone $requete)->count(),
            'nbValides' => (clone $requete)->where('statut', 'valide')->count(),
            'nbEnAttente' => (clone $requete)->whereIn('statut', ['soumis', 'en_verification'])->count(),
            'nbACorriger' => (clone $requete)->where('statut', 'a_corriger')->count(),
            'nbRejetes' => (clone $requete)->where('statut', 'rejete')->count(),
            'derniers' => $requete->with(['filiere', 'annee'])
                ->orderByDesc('date_depot')
                ->limit(5)
                ->get(),
            'suggestions' => $suggestions,
            'consultations' => $consultations,
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
