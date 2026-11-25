<?php

namespace Tests\Concerns;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;

trait CreeDonneesMemoires
{
    protected function creerFiliere(string $code = 'INFO'): Filiere
    {
        return Filiere::firstOrCreate(
            ['code' => $code],
            [
                'nom' => 'Informatique de gestion',
                'description' => 'Filière de Licence (Bac+3)',
            ]
        );
    }

    protected function creerAnnee(string $libelle = '2025-2026'): AnneeAcademique
    {
        return AnneeAcademique::firstOrCreate(
            ['libelle' => $libelle],
            [
                'date_debut' => '2025-10-01',
                'date_fin' => '2026-07-31',
                'active' => true,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $attributs
     */
    protected function creerMemoire(array $attributs = []): Memoire
    {
        return Memoire::create(array_merge([
            'titre' => 'Mémoire de test',
            'resume' => 'Résumé de test.',
            'mots_cles' => 'test, mémoire',
            'fichier_pdf' => 'memoires/test.pdf',
            'statut' => 'valide',
            'confidentialite' => 'public',
            'telechargement_autorise' => true,
            'auteur_nom' => 'Auteur Test',
            'type_diplome' => 'Licence',
            'filiere_id' => $this->creerFiliere()->id,
            'annee_id' => $this->creerAnnee()->id,
            'date_depot' => now(),
        ], $attributs));
    }
}
