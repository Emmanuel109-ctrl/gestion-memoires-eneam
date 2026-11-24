<?php

namespace Database\Seeders;

use App\Models\Memoire;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Historique de démonstration pour le flux "Activités récentes" du
 * tableau de bord bibliothécaire.
 */
class ValidationSeeder extends Seeder
{
    public function run(): void
    {
        $bibliothecaire = User::where('role', 'bibliothecaire')->first();

        $decisions = [
            [
                'cote' => 'INFO-2026-001',
                'statut' => 'valide',
                'motif' => null,
                'conforme_version_papier' => true,
                'format_conforme' => true,
                'date_decision' => '2026-06-25 10:00:00',
            ],
            [
                'cote' => 'GC-2026-003',
                'statut' => 'valide',
                'motif' => null,
                'conforme_version_papier' => true,
                'format_conforme' => true,
                'date_decision' => '2026-06-04 15:10:00',
            ],
            [
                'cote' => 'ECO-2026-021',
                'statut' => 'rejete',
                'motif' => 'Le fichier PDF ne correspond pas à la version physique déposée.',
                'conforme_version_papier' => true,
                'format_conforme' => false,
                'date_decision' => '2026-09-05 09:30:00',
            ],
            [
                'cote' => 'INFO-2026-032',
                'statut' => 'a_corriger',
                'motif' => 'La page de garde et la notice bibliographique doivent être complétées.',
                'conforme_version_papier' => true,
                'format_conforme' => true,
                'date_decision' => '2026-11-13 14:20:00',
            ],
        ];

        foreach ($decisions as $decision) {
            $memoire = Memoire::where('cote', $decision['cote'])->first();

            if ($memoire === null) {
                continue;
            }

            Validation::updateOrCreate(
                ['memoire_id' => $memoire->id],
                [
                    'validateur_id' => $bibliothecaire?->id,
                    'statut' => $decision['statut'],
                    'motif' => $decision['motif'],
                    'conforme_version_papier' => $decision['conforme_version_papier'],
                    'format_conforme' => $decision['format_conforme'],
                    'date_decision' => Carbon::parse($decision['date_decision']),
                ]
            );
        }
    }
}
