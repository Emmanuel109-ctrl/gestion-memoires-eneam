<?php

namespace Tests\Feature;

use App\Models\EtudiantEneam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RosterImportTest extends TestCase
{
    use RefreshDatabase;

    private ?string $fichier = null;

    protected function tearDown(): void
    {
        if ($this->fichier !== null && is_file($this->fichier)) {
            unlink($this->fichier);
        }

        parent::tearDown();
    }

    private function ecrireCsv(string $contenu): string
    {
        $this->fichier = tempnam(sys_get_temp_dir(), 'eneam-roster-');
        file_put_contents($this->fichier, $contenu);

        return $this->fichier;
    }

    public function test_roster_is_imported_and_updated(): void
    {
        $csv = $this->ecrireCsv(<<<'CSV'
matricule;nom;email;filiere;promotion;actif
20240001;Awa HOUNKPE;awa.hounkpe@eneam.bj;Informatique de Gestion;2025-2026;1
20240002;Kossi ADJOVI;kossi.adjovi@eneam.bj;Gestion;2025-2026;1
CSV);

        $this->artisan('eneam:importer-etudiants', ['fichier' => $csv, '--source' => 'test'])
            ->assertSuccessful();

        $this->assertDatabaseHas('etudiants_eneam', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'source' => 'test',
        ]);
        $this->assertSame(2, EtudiantEneam::count());

        $csv2 = $this->ecrireCsv(<<<'CSV'
matricule;nom;email;filiere;promotion;actif
20240001;Awa HOUNKPE;awa.nouvelle@eneam.bj;Informatique de Gestion;2025-2026;0
CSV);

        $this->artisan('eneam:importer-etudiants', ['fichier' => $csv2])
            ->assertSuccessful();

        $this->assertDatabaseHas('etudiants_eneam', [
            'matricule' => '20240001',
            'email' => 'awa.nouvelle@eneam.bj',
            'actif' => false,
        ]);
    }

    public function test_dry_run_does_not_write_anything(): void
    {
        $csv = $this->ecrireCsv(<<<'CSV'
matricule;nom;email;filiere;promotion;actif
20240001;Awa HOUNKPE;awa.hounkpe@eneam.bj;Informatique de Gestion;2025-2026;1
CSV);

        $this->artisan('eneam:importer-etudiants', [
            'fichier' => $csv,
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount('etudiants_eneam', 0);
    }

    public function test_invalid_rows_are_reported_and_skipped(): void
    {
        $csv = $this->ecrireCsv(<<<'CSV'
matricule;nom;email;filiere;promotion;actif
123;Nom Invalide;invalide@eneam.bj;Informatique;2025-2026;1
20240002;Kossi ADJOVI;pas-un-email;Gestion;2025-2026;1
20240003;Étudiant Valide;valide@eneam.bj;Gestion;2025-2026;1
CSV);

        $this->artisan('eneam:importer-etudiants', ['fichier' => $csv])
            ->assertFailed();

        $this->assertDatabaseMissing('etudiants_eneam', ['matricule' => '123']);
        $this->assertDatabaseHas('etudiants_eneam', ['matricule' => '20240003']);
    }
}
