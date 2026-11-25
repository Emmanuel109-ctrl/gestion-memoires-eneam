<?php

namespace Database\Seeders;

use App\Models\EtudiantEneam;
use Illuminate\Database\Seeder;

/**
 * Annuaire de démonstration.
 *
 * Le fichier réel `database/data/etudiants_eneam.csv` est ignoré par Git.
 * Ces entrées permettent de tester l'activation et la connexion sans
 * manipuler de vraies données personnelles.
 */
class EtudiantEneamSeeder extends Seeder
{
    public function run(): void
    {
        $etudiants = [
            [
                'matricule' => '20240001',
                'nom' => 'Awa HOUNKPE',
                'email' => 'etudiant@eneam.bj',
                'filiere' => 'Informatique de Gestion',
                'promotion' => '2025-2026',
                'actif' => true,
            ],
            [
                'matricule' => '20240002',
                'nom' => 'Kossi ADJOVI',
                'email' => 'etudiant2@eneam.bj',
                'filiere' => 'Gestion',
                'promotion' => '2025-2026',
                'actif' => true,
            ],
            [
                'matricule' => '20249999',
                'nom' => 'ETUDIANT Démo',
                'email' => 'premiere.connexion@eneam.bj',
                'filiere' => 'Informatique de Gestion',
                'promotion' => '2025-2026',
                'actif' => true,
            ],
        ];

        foreach ($etudiants as $etudiant) {
            EtudiantEneam::updateOrCreate(
                ['matricule' => $etudiant['matricule']],
                [
                    ...$etudiant,
                    'source' => 'seed-demo',
                    'synced_at' => now(),
                ]
            );
        }
    }
}
