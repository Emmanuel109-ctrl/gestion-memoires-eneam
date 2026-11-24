<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use App\Models\Validation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ValidationController extends Controller
{
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

        $memoire->load(['filiere', 'annee', 'etudiant']);
        $validation = $memoire->validations()->latest('id')->first();

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
            Validation::updateOrCreate(
                ['memoire_id' => $memoire->id],
                [
                    'validateur_id' => $request->user()->id,
                    'statut' => $validated['statut'],
                    'motif' => $validated['motif'] ?? null,
                    'conforme_version_papier' => $request->boolean('conforme_version_papier'),
                    'format_conforme' => $request->boolean('format_conforme'),
                    'date_decision' => now(),
                ]
            );

            $memoire->update(['statut' => $validated['statut']]);
        });

        return redirect()
            ->route('validations.index')
            ->with('success', 'La décision a été enregistrée.');
    }
}
