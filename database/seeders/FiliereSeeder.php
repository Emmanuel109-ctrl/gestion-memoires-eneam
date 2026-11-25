<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Memoire;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            ['nom' => 'Informatique de gestion', 'code' => 'INFO', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Assurance', 'code' => 'ASS', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Banque et Finances de Marché (BFM)', 'code' => 'BFM', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Banque et Institutions de Microfinance (BIMF)', 'code' => 'BIMF', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Développement Local et Régional (DLR)', 'code' => 'DLR', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Économie et Gestion de l’Environnement (EGE)', 'code' => 'EGE', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Gestion Commerciale (GC)', 'code' => 'GC', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Gestion des Ressources Humaines (GRH)', 'code' => 'GRH', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Gestion des Transports et Logistique (GTL)', 'code' => 'GTL', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Gestion Financière et Comptable (GFC)', 'code' => 'GFC', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Planification et Gestion des Projets (PGP)', 'code' => 'PGP', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Statistique Démographique et Sociale (SDS)', 'code' => 'SDS', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Statistique Économique et Sectorielle (SES)', 'code' => 'SES', 'description' => 'Filière de Licence (Bac+3)'],
            ['nom' => 'Diplôme d’Études de Comptabilité et de Gestion Financière (DECOGEF)', 'code' => 'DECOGEF', 'description' => 'Diplôme d’Études de Comptabilité et de Gestion Financière'],
        ];

        DB::transaction(function () use ($filieres): void {
            Filiere::upsert($filieres, ['code'], ['nom', 'description']);

            foreach ([
                'AIP' => 'INFO',
                'ARI' => 'INFO',
                'GEST' => 'GC',
                'AGRO' => 'DLR',
                'ECO' => 'BFM',
            ] as $ancienCode => $nouveauCode) {
                $ancienne = Filiere::where('code', $ancienCode)->first();
                $nouvelle = Filiere::where('code', $nouveauCode)->first();

                if ($ancienne === null || $nouvelle === null) {
                    continue;
                }

                Memoire::where('filiere_id', $ancienne->id)
                    ->update(['filiere_id' => $nouvelle->id]);

                $ancienne->delete();
            }

            $genieCivil = Filiere::where('code', 'GENCIV')->first();

            if ($genieCivil !== null) {
                Memoire::where('filiere_id', $genieCivil->id)
                    ->where('fichier_hash', hash('sha256', 'memoire-demo-5'))
                    ->delete();

                if ($genieCivil->memoires()->exists()) {
                    throw new \RuntimeException(
                        'Des mémoires utilisent encore la filière Génie Civil. Reclassez-les avant de supprimer cette filière.'
                    );
                }

                $genieCivil->delete();
            }
        });
    }
}
