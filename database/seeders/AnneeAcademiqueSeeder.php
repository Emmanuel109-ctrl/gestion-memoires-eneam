<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnneeAcademiqueSeeder extends Seeder
{
    public function run(): void
    {
        $annees = [
            ['libelle' => '2023-2024', 'date_debut' => '2023-10-01', 'date_fin' => '2024-07-31', 'active' => false],
            ['libelle' => '2024-2025', 'date_debut' => '2024-10-01', 'date_fin' => '2025-07-31', 'active' => false],
            ['libelle' => '2025-2026', 'date_debut' => '2025-10-01', 'date_fin' => '2026-07-31', 'active' => false],
            ['libelle' => '2026-2027', 'date_debut' => '2026-10-01', 'date_fin' => '2027-07-31', 'active' => true],
        ];

        DB::transaction(function () use ($annees): void {
            AnneeAcademique::query()->where('active', true)->update(['active' => false]);

            foreach ($annees as $annee) {
                AnneeAcademique::updateOrCreate(
                    ['libelle' => $annee['libelle']],
                    $annee
                );
            }
        });
    }
}
