<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
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
        $awa = User::where('matricule', 'ETU-2024-001')->first();
        $kossi = User::where('matricule', 'ETU-2024-002')->first();

        $filieres = Filiere::pluck('id', 'code');
        $annees = AnneeAcademique::pluck('id', 'libelle');

        $memoires = [
            [
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
            [
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
                'cote' => 'GEST-2026-014',
                'filiere_id' => $filieres['GEST'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 43,
                'date_depot' => '2026-07-05 14:40:00',
            ],
            [
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
                'cote' => 'AGRO-2026-007',
                'filiere_id' => $filieres['AGRO'],
                'annee_id' => $annees['2026-2027'],
                'vues' => 0,
                'date_depot' => '2026-11-10 10:05:00',
            ],
            [
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
                'cote' => 'ECO-2026-021',
                'filiere_id' => $filieres['ECO'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 12,
                'date_depot' => '2026-09-02 16:20:00',
            ],
            [
                'titre' => 'Dimensionnement d\'un pont en béton armé sur le fleuve Ouémé',
                'resume' => 'Étude technique et calcul de structure pour un ouvrage de franchissement à usage mixte.',
                'mots_cles' => 'génie civil, béton armé, pont, structure',
                'statut' => 'valide',
                'confidentialite' => 'restreint',
                'telechargement_autorise' => false,
                'etudiant_id' => $kossi?->id,
                'auteur_nom' => $kossi?->name,
                'type_diplome' => 'Master',
                'date_soutenance' => '2026-05-28',
                'cote' => 'GC-2026-003',
                'filiere_id' => $filieres['GC'],
                'annee_id' => $annees['2025-2026'],
                'vues' => 76,
                'date_depot' => '2026-06-01 11:00:00',
            ],
        ];

        foreach ($memoires as $index => $memoire) {
            $memoire['fichier_pdf'] = 'memoires/demo/demo-'.($index + 1).'.pdf';
            $memoire['fichier_hash'] = hash('sha256', 'memoire-demo-'.($index + 1));

            Memoire::updateOrCreate(
                ['fichier_hash' => $memoire['fichier_hash']],
                $memoire
            );
        }
    }
}
