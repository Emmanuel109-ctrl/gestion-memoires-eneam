<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnneeAcademiqueController extends Controller
{
    public function index(): View
    {
        $annees = AnneeAcademique::query()
            ->orderByDesc('date_debut')
            ->get();

        return view('annees.index', compact('annees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:20', 'unique:annees_academiques,libelle'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'active' => ['sometimes', 'boolean'],
        ]);
        $requestedActive = $request->boolean('active');

        DB::transaction(function () use ($validated, $requestedActive): void {
            $active = $requestedActive || ! AnneeAcademique::query()->where('active', true)->exists();

            if ($active) {
                AnneeAcademique::query()->where('active', true)->update(['active' => false]);
            }

            AnneeAcademique::create([
                'libelle' => $validated['libelle'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'active' => $active,
            ]);
        });

        return redirect()->route('annees.index')->with('success', 'L’année académique a été créée.');
    }

    public function activate(AnneeAcademique $annee): RedirectResponse
    {
        DB::transaction(function () use ($annee): void {
            AnneeAcademique::query()->where('active', true)->update(['active' => false]);
            $annee->update(['active' => true]);
        });

        return redirect()->route('annees.index')->with('success', 'L’année académique active a été mise à jour.');
    }
}
