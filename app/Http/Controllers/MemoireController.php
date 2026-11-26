<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\HistoriqueLecture;
use App\Models\Memoire;
use App\Models\VersionMemoire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class MemoireController extends Controller
{
    public function gestion(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'statut' => ['nullable', Rule::in(['soumis', 'en_verification', 'a_corriger', 'valide', 'rejete'])],
        ]);

        $requete = Memoire::query()->with(['filiere', 'annee', 'etudiant']);

        if (($recherche = trim((string) ($filtres['q'] ?? ''))) !== '') {
            $requete->where(function ($query) use ($recherche): void {
                $motif = '%'.$recherche.'%';
                $query->where('titre', 'like', $motif)
                    ->orWhere('auteur_nom', 'like', $motif)
                    ->orWhereHas('etudiant', fn ($etudiant) => $etudiant->where('name', 'like', $motif));
            });
        }

        if (! empty($filtres['statut'])) {
            $requete->where('statut', $filtres['statut']);
        }

        return view('memoires.index', [
            'memoires' => $requete->orderByDesc('date_depot')->paginate(15)->withQueryString(),
            'filtres' => $filtres,
        ]);
    }

    public function miennes(Request $request): View
    {
        $memoires = Memoire::query()
            ->with(['filiere', 'annee', 'validations', 'versions'])
            ->where('etudiant_id', $request->user()->id)
            ->orderByDesc('date_depot')
            ->paginate(10);

        return view('memoires.miennes', compact('memoires'));
    }

    public function creer(): View
    {
        return view('memoires.create', [
            'filieres' => Filiere::orderBy('nom')->get(),
            'annees' => AnneeAcademique::where('active', true)->orderByDesc('date_debut')->get(),
        ]);
    }

    public function stocker(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'resume' => ['nullable', 'string', 'max:10000'],
            'mots_cles' => ['nullable', 'string', 'max:1000'],
            'type_diplome' => ['required', Rule::in(['Licence', 'DECOGEF'])],
            'filiere_id' => ['required', 'integer', 'exists:filieres,id'],
            'annee_id' => ['required', 'integer', Rule::exists('annees_academiques', 'id')->where('active', true)],
            'fichier' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $fichier = $donnees['fichier'];
        $hash = hash_file('sha256', $fichier->getRealPath());

        if (Memoire::where('fichier_hash', $hash)->exists()) {
            throw ValidationException::withMessages([
                'fichier' => 'Ce fichier PDF a déjà été déposé.',
            ]);
        }

        $etudiant = $request->user();
        $chemin = $fichier->storeAs('memoires/'.$etudiant->id, Str::uuid().'.pdf', 'local');

        if ($chemin === false) {
            throw new \RuntimeException('Le fichier PDF n’a pas pu être enregistré.');
        }

        DB::transaction(function () use ($donnees, $etudiant, $chemin, $hash): void {
            $memoire = Memoire::create([
                'titre' => $donnees['titre'],
                'resume' => $donnees['resume'] ?? null,
                'mots_cles' => $donnees['mots_cles'] ?? null,
                'fichier_pdf' => $chemin,
                'fichier_hash' => $hash,
                'statut' => 'soumis',
                'confidentialite' => 'interne',
                'etudiant_id' => $etudiant->id,
                'auteur_nom' => $etudiant->name,
                'type_diplome' => $donnees['type_diplome'],
                'filiere_id' => $donnees['filiere_id'],
                'annee_id' => $donnees['annee_id'],
                'date_depot' => now(),
            ]);

            VersionMemoire::create([
                'memoire_id' => $memoire->id,
                'fichier_pdf' => $chemin,
                'numero_version' => 1,
            ]);
        });

        return redirect()
            ->route('memoires.miennes')
            ->with('success', 'Votre mémoire a été déposé et ajouté à la file de vérification.');
    }

    public function resoumettre(Request $request, Memoire $memoire): RedirectResponse
    {
        abort_unless((int) $memoire->etudiant_id === (int) $request->user()->id, 404);
        abort_unless($memoire->statut === 'a_corriger', 403, 'Ce mémoire ne nécessite pas de correction.');

        $donnees = $request->validate([
            'fichier' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $fichier = $donnees['fichier'];
        $hash = hash_file('sha256', $fichier->getRealPath());

        if (Memoire::where('fichier_hash', $hash)->whereKeyNot($memoire->id)->exists()) {
            throw ValidationException::withMessages([
                'fichier' => 'Ce fichier PDF a déjà été déposé pour un autre mémoire.',
            ]);
        }

        $chemin = $fichier->storeAs(
            'memoires/'.$request->user()->id,
            Str::uuid().'.pdf',
            'local'
        );

        if ($chemin === false) {
            throw new \RuntimeException('Le fichier PDF corrigé n’a pas pu être enregistré.');
        }

        DB::transaction(function () use ($memoire, $chemin, $hash): void {
            $numeroVersion = (int) $memoire->versions()->max('numero_version') + 1;

            VersionMemoire::create([
                'memoire_id' => $memoire->id,
                'fichier_pdf' => $chemin,
                'numero_version' => $numeroVersion,
            ]);

            $memoire->update([
                'fichier_pdf' => $chemin,
                'fichier_hash' => $hash,
                'statut' => 'soumis',
                'date_depot' => now(),
            ]);
        });

        return redirect()
            ->route('memoires.miennes')
            ->with('success', 'Votre version corrigée a été envoyée à la bibliothèque.');
    }

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
