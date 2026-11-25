<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\HistoriqueLecture;
use App\Models\Memoire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class MemoireController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'filiere' => ['nullable', 'integer', 'exists:filieres,id'],
            'annee' => ['nullable', 'integer', 'exists:annees_academiques,id'],
        ]);

        $requete = Memoire::query()
            ->with(['filiere', 'annee'])
            ->visiblesPour($request->user())
            ->where('statut', 'valide');

        if (($recherche = trim((string) ($filtres['q'] ?? ''))) !== '') {
            $requete->where(function ($q) use ($recherche): void {
                $motif = '%'.$recherche.'%';

                $q->where('titre', 'like', $motif)
                    ->orWhere('resume', 'like', $motif)
                    ->orWhere('mots_cles', 'like', $motif)
                    ->orWhere('auteur_nom', 'like', $motif);
            });
        }

        if (! empty($filtres['filiere'])) {
            $requete->where('filiere_id', $filtres['filiere']);
        }

        if (! empty($filtres['annee'])) {
            $requete->where('annee_id', $filtres['annee']);
        }

        $memoires = $requete
            ->orderByDesc('date_depot')
            ->paginate(12)
            ->withQueryString();

        return view('catalogue.index', [
            'memoires' => $memoires,
            'filieres' => Filiere::orderBy('nom')->get(),
            'annees' => AnneeAcademique::orderByDesc('date_debut')->get(),
            'filtres' => $filtres,
            'recherche' => $recherche ?? '',
        ]);
    }

    public function show(Memoire $memoire): View
    {
        $this->authorize('view', $memoire);

        $memoire->load(['filiere', 'annee']);

        $utilisateur = request()->user();

        return view('catalogue.show', [
            'memoire' => $memoire,
            'peutLire' => $utilisateur !== null && $utilisateur->can('read', $memoire),
            'peutTelecharger' => $utilisateur !== null && $utilisateur->can('download', $memoire),
        ]);
    }

    /**
     * Page de garde publique : uniquement l'image, jamais le PDF complet.
     */
    public function couverture(Memoire $memoire, \App\Services\CouvertureService $couvertures): Response
    {
        $this->authorize('view', $memoire);

        return $couvertures->reponse($memoire);
    }

    public function lire(Memoire $memoire): BinaryFileResponse
    {
        $this->authorize('read', $memoire);

        $chemin = $this->chemin($memoire);

        $this->journaliser($memoire, 'lecture');
        $memoire->increment('vues');

        return response()
            ->file($chemin, ['Content-Type' => 'application/pdf'])
            ->setContentDisposition('inline', $this->nomFichier($memoire));
    }

    public function telecharger(Memoire $memoire): BinaryFileResponse
    {
        $this->authorize('download', $memoire);

        $chemin = $this->chemin($memoire);

        $this->journaliser($memoire, 'telechargement');

        return response()->download($chemin, $this->nomFichier($memoire), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function chemin(Memoire $memoire): string
    {
        abort_unless(
            Storage::disk('local')->exists($memoire->fichier_pdf),
            404,
            "Le fichier de ce mémoire n'est pas encore disponible."
        );

        return Storage::disk('local')->path($memoire->fichier_pdf);
    }

    private function nomFichier(Memoire $memoire): string
    {
        return (Str::slug($memoire->titre) ?: 'memoire').'.pdf';
    }

    private function journaliser(Memoire $memoire, string $type): void
    {
        if (auth()->check()) {
            HistoriqueLecture::create([
                'user_id' => auth()->id(),
                'memoire_id' => $memoire->id,
                'type' => $type,
            ]);
        }
    }
}
