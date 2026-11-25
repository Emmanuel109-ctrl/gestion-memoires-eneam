<?php

namespace Tests\Feature;

use App\Models\Memoire;
use Database\Seeders\AnneeAcademiqueSeeder;
use Database\Seeders\FiliereSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemoireImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_import_copies_the_pdf_into_private_storage(): void
    {
        Storage::fake('local');
        $this->seed(FiliereSeeder::class);
        $this->seed(AnneeAcademiqueSeeder::class);

        $dossier = sys_get_temp_dir().DIRECTORY_SEPARATOR.'eneam-memoires-'.uniqid();
        mkdir($dossier);
        $pdf = $dossier.DIRECTORY_SEPARATOR.'memoire-test.pdf';
        file_put_contents($pdf, '%PDF-1.4 contenu de test');

        $manifeste = $dossier.DIRECTORY_SEPARATOR.'memoires.csv';
        file_put_contents($manifeste, implode("\n", [
            'fichier;titre;auteur;matricule;filiere;annee;type_diplome;confidentialite;telechargement_autorise;resume;mots_cles;date_soutenance;date_embargo;couverture_page',
            '"memoire-test.pdf";"Mémoire importé";"Étudiant Test";"";"INFO";"2025-2026";"Licence";"public";"1";"Résumé.";"test";"2026-07-31";"";"0"',
        ]));

        $this->artisan('eneam:importer-memoires', [
            '--manifeste' => $manifeste,
            '--source' => $dossier,
            '--sans-couvertures' => true,
        ])->assertSuccessful();

        $memoire = Memoire::where('titre', 'Mémoire importé')->firstOrFail();

        $this->assertSame('valide', $memoire->statut);
        $this->assertTrue(Storage::disk('local')->exists($memoire->fichier_pdf));
        $this->assertSame(
            hash_file('sha256', $pdf),
            $memoire->fichier_hash
        );

        unlink($pdf);
        unlink($manifeste);
        rmdir($dossier);
    }
}
