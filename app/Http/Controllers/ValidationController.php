<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use App\Models\Validation;
use App\Notifications\DecisionMemoireNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ValidationController extends Controller
{
    public function historique(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'statut' => ['nullable', Rule::in(['a_corriger', 'valide', 'rejete'])],
        ]);

        return view('validations.historique', [
            'validations' => $this->requeteHistorique($filtres)
                ->orderByDesc('date_decision')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString(),
            'filtres' => $filtres,
        ]);
    }

    public function exporterHistorique(Request $request): StreamedResponse
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'statut' => ['nullable', Rule::in(['a_corriger', 'valide', 'rejete'])],
        ]);
        $validations = $this->requeteHistorique($filtres)
            ->orderByDesc('date_decision')
            ->orderByDesc('id');

        return response()->streamDownload(function () use ($validations): void {
            $sortie = fopen('php://output', 'w');

            if ($sortie === false) {
                throw new \RuntimeException('Impossible de créer le fichier CSV de l’historique.');
            }

            fwrite($sortie, "\xEF\xBB\xBF");
            fputcsv($sortie, [
                'Date de décision',
                'Mémoire',
                'Auteur',
                'Filière',
                'Année académique',
                'Décision',
                'Responsable',
                'Motif ou commentaire',
            ], ';', '"', '\\');

            $validations->chunk(500, function ($lot) use ($sortie): void {
                foreach ($lot as $validation) {
                    $memoire = $validation->memoire;
                    $ligne = [
                        $validation->date_decision?->format('Y-m-d H:i:s') ?? '',
                        $memoire?->titre ?? 'Mémoire supprimé',
                        $memoire?->etudiant?->name ?? $memoire?->auteur_nom ?? '',
                        $memoire?->filiere?->nom ?? '',
                        $memoire?->annee?->libelle ?? '',
                        $validation->statut,
                        $validation->validateur?->name ?? 'Compte supprimé',
                        $validation->motif ?? '',
                    ];

                    fputcsv(
                        $sortie,
                        array_map(fn ($valeur) => $this->protegerCelluleCsv((string) $valeur), $ligne),
                        ';',
                        '"',
                        '\\'
                    );
                }
            });

            fclose($sortie);
        }, 'historique-decisions-'.now()->format('Ymd').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function index(): View
    {
        $memoires = Memoire::with(['filiere', 'annee', 'etudiant'])
            ->whereIn('statut', ['soumis', 'en_verification'])
            ->orderBy('date_depot')
            ->paginate(10);

        $debutDuMois = now()->startOfMonth();
        $finDuMois = now()->endOfMonth();

        return view('validations.index', [
            'memoires' => $memoires,
            'aVerifier' => Memoire::whereIn('statut', ['soumis', 'en_verification'])->count(),
            'validesCeMois' => Validation::where('statut', 'valide')
                ->whereBetween('date_decision', [$debutDuMois, $finDuMois])
                ->count(),
            'rejetesCeMois' => Validation::where('statut', 'rejete')
                ->whereBetween('date_decision', [$debutDuMois, $finDuMois])
                ->count(),
        ]);
    }

    public function show(Memoire $memoire): View
    {
        abort_unless(in_array($memoire->statut, ['soumis', 'en_verification'], true), 404);

        $memoire->load([
            'filiere',
            'annee',
            'etudiant',
            'validations' => fn ($query) => $query
                ->with('validateur')
                ->orderByDesc('date_decision')
                ->orderByDesc('id'),
        ]);
        $validation = $memoire->validations->first();

        return view('validations.show', compact('memoire', 'validation'));
    }

    public function update(Request $request, Memoire $memoire): RedirectResponse
    {
        abort_unless(in_array($memoire->statut, ['soumis', 'en_verification'], true), 404);

        $validated = $request->validate([
            'statut' => ['required', Rule::in(['valide', 'a_corriger', 'rejete'])],
            'motif' => ['nullable', 'string', 'required_if:statut,a_corriger,rejete', 'max:5000'],
            'conforme_version_papier' => ['sometimes', 'boolean'],
            'format_conforme' => ['sometimes', 'boolean'],
        ], [
            'motif.required_if' => 'Un commentaire est obligatoire pour demander une correction ou rejeter le mémoire.',
        ]);

        DB::transaction(function () use ($memoire, $request, $validated): void {
            Validation::create([
                'memoire_id' => $memoire->id,
                'validateur_id' => $request->user()->id,
                'statut' => $validated['statut'],
                'motif' => $validated['motif'] ?? null,
                'conforme_version_papier' => $request->boolean('conforme_version_papier'),
                'format_conforme' => $request->boolean('format_conforme'),
                'date_decision' => now(),
            ]);

            $memoire->update(['statut' => $validated['statut']]);
        });

        $memoire->loadMissing('etudiant');
        $memoire->etudiant?->notify(new DecisionMemoireNotification(
            $memoire,
            $validated['statut'],
            $validated['motif'] ?? null
        ));

        return redirect()
            ->route('validations.index')
            ->with('success', 'La décision a été enregistrée.');
    }

    /**
     * @param  array{q?: string|null, statut?: string|null}  $filtres
     */
    private function requeteHistorique(array $filtres): Builder
    {
        $requete = Validation::query()
            ->with(['memoire.etudiant', 'memoire.filiere', 'memoire.annee', 'validateur'])
            ->whereNotNull('date_decision');

        if (! empty($filtres['statut'])) {
            $requete->where('statut', $filtres['statut']);
        }

        if (($recherche = trim((string) ($filtres['q'] ?? ''))) !== '') {
            $requete->whereHas('memoire', function ($memoire) use ($recherche): void {
                $motif = '%'.$recherche.'%';
                $memoire->where('titre', 'like', $motif)
                    ->orWhere('auteur_nom', 'like', $motif)
                    ->orWhereHas('etudiant', fn ($etudiant) => $etudiant->where('name', 'like', $motif));
            });
        }

        return $requete;
    }

    private function protegerCelluleCsv(string $valeur): string
    {
        return preg_match('/^[\x00-\x20]*[=+\-@]/', $valeur) === 1
            ? "'".$valeur
            : $valeur;
    }
}
