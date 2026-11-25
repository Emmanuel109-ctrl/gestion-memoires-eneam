<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
use App\Services\ImportateurMemoires;
use Illuminate\Database\Seeder;

/**
 * Jeu de démonstration : quelques mémoires fictifs pour que les trois
 * tableaux de bord affichent de vraies données dès le premier lancement.
 *
 * Les fichiers PDF correspondants seront ajoutés au lot 2 (dépôt + lecteur).
 */
class MemoireSeeder extends Seeder
{
    public function run(): void
    {
        // Si un manifeste local existe, il remplace le jeu fictif : les
        // notices réelles restent hors Git, seules les métadonnées sont lues.
        $manifeste = database_path('data/memoires.csv');

        if (is_file($manifeste)) {
            app(ImportateurMemoires::class)->importer(
                $manifeste,
                null,
                false,
                false,
                false
            );

            return;
        }

        $awa = User::where('matricule', '20240001')->first();
        $kossi = User::where('matricule', '20240002')->first();

        $filieres = Filiere::pluck('id', 'code');
        $annees = AnneeAcademique::pluck('id', 'libelle');

        $memoires = [
            1 => [
                'titre' => 'Conception d\'un système de gestion des mémoires universitaires',
                'resume' => 'Étude et réalisation d\'une application web pour le dépôt, la validation et la consultation des mémoires de l\'ENEAM.',
                'mots_cles' => 'gestion documentaire, Laravel, bibliothèque numérique',
                'statut' => 'valide',
                'confidentialite' => 'public',
                'telechargement_autorise' => true,
                'etudiant_id' => $awa?->id,
                'auteur_nom' => $awa?->name,
                'type_diplome' => 'Master',
                'date_soutenance' => '2026-06-18',
                'cote' => 'INFO-2026-001',
                'filiere_id' => $filieres['INFO'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 128,
                'date_depot' => '2026-06-22 09:15:00',
            ],
            2 => [
                'titre' => 'Analyse des performances des PME béninoises',
                'resume' => 'Diagnostic des facteurs de croissance et de compétitivité des petites et moyennes entreprises au Bénin.',
                'mots_cles' => 'PME, performance, compétitivité, Bénin',
                'statut' => 'en_verification',
                'confidentialite' => 'interne',
                'telechargement_autorise' => true,
                'etudiant_id' => $kossi?->id,
                'auteur_nom' => $kossi?->name,
                'type_diplome' => 'Licence',
                'date_soutenance' => '2026-07-02',
                'cote' => 'GC-2026-014',
                'filiere_id' => $filieres['GC'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 43,
                'date_depot' => '2026-07-05 14:40:00',
            ],
            3 => [
                'titre' => 'Optimisation de la production agricole par l\'intelligence artificielle',
                'resume' => 'Proposition d\'un modèle de prédiction des rendements agricoles à partir de données climatiques.',
                'mots_cles' => 'agriculture, intelligence artificielle, rendement',
                'statut' => 'soumis',
                'confidentialite' => 'public',
                'telechargement_autorise' => false,
                'etudiant_id' => $awa?->id,
                'auteur_nom' => $awa?->name,
                'type_diplome' => 'Master',
                'date_soutenance' => '2026-09-12',
                'cote' => 'DLR-2026-007',
                'filiere_id' => $filieres['DLR'],
                'annee_id' => $annees['2026-2027'],
                'vues' => 0,
                'date_depot' => '2026-11-10 10:05:00',
            ],
            4 => [
                'titre' => 'Impact de la fiscalité sur l\'investissement privé au Bénin',
                'resume' => 'Évaluation des effets de la politique fiscale sur les décisions d\'investissement des entreprises.',
                'mots_cles' => 'fiscalité, investissement, politique économique',
                'statut' => 'rejete',
                'confidentialite' => 'public',
                'telechargement_autorise' => true,
                'etudiant_id' => $kossi?->id,
                'auteur_nom' => $kossi?->name,
                'type_diplome' => 'Licence',
                'date_soutenance' => '2026-08-30',
                'cote' => 'BFM-2026-021',
                'filiere_id' => $filieres['BFM'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 12,
                'date_depot' => '2026-09-02 16:20:00',
            ],
            6 => [
                'titre' => 'Mise en place d\'un tableau de bord de suivi des stocks pharmaceutiques',
                'resume' => 'Conception d\'un outil de pilotage des entrées, sorties et seuils d\'alerte dans les officines.',
                'mots_cles' => 'stock, pharmacie, tableau de bord, gestion',
                'statut' => 'a_corriger',
                'confidentialite' => 'public',
                'telechargement_autorise' => true,
                'etudiant_id' => $awa?->id,
                'auteur_nom' => $awa?->name,
                'type_diplome' => 'Master',
                'date_soutenance' => '2026-10-05',
                'cote' => 'INFO-2026-032',
                'filiere_id' => $filieres['INFO'],
                'annee_id' => $annees['2026-2027'],
                'vues' => 5,
                'date_depot' => '2026-11-12 08:30:00',
            ],
        ];

        foreach ($memoires as $index => $memoire) {
            $memoire['fichier_pdf'] = 'memoires/demo/demo-'.$index.'.pdf';
            $memoire['fichier_hash'] = hash('sha256', 'memoire-demo-'.$index);

            Memoire::updateOrCreate(
                ['fichier_hash' => $memoire['fichier_hash']],
                $memoire
            );
        }
    }
}
