<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
use App\Models\Validation;
use App\Models\VersionMemoire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentMemoireSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_student_can_submit_a_private_pdf_for_review(): void
    {
        $student = User::factory()->create(['role' => 'etudiant']);
        $filiere = Filiere::create([
            'nom' => 'Informatique de gestion',
            'code' => 'INFO',
        ]);
        $annee = AnneeAcademique::create([
            'libelle' => '2026-2027',
            'date_debut' => '2026-10-01',
            'date_fin' => '2027-07-31',
            'active' => true,
        ]);

        $response = $this->actingAs($student)->post(route('memoires.store'), [
            'titre' => 'Étude des systèmes numériques',
            'resume' => 'Résumé de démonstration',
            'mots_cles' => 'numérique, systèmes',
            'type_diplome' => 'Licence',
            'filiere_id' => $filiere->id,
            'annee_id' => $annee->id,
            'fichier' => UploadedFile::fake()->createWithContent('memoire.pdf', "%PDF-1.4\ncontenu"),
        ]);

        $response->assertRedirect(route('memoires.miennes'));
        $memoire = Memoire::where('titre', 'Étude des systèmes numériques')->firstOrFail();

        $this->assertSame('soumis', $memoire->statut);
        $this->assertSame($student->id, $memoire->etudiant_id);
        $this->assertDatabaseHas('versions_memoires', [
            'memoire_id' => $memoire->id,
            'numero_version' => 1,
        ]);
        Storage::disk('local')->assertExists($memoire->fichier_pdf);
        $this->assertDatabaseMissing('memoires', [
            'id' => $memoire->id,
            'confidentialite' => 'public',
        ]);
    }

    public function test_student_can_resubmit_a_corrected_pdf_and_keep_the_previous_version(): void
    {
        $student = User::factory()->create(['role' => 'etudiant']);
        $filiere = Filiere::create(['nom' => 'Assurance', 'code' => 'ASS']);
        $annee = AnneeAcademique::create([
            'libelle' => '2026-2027',
            'date_debut' => '2026-10-01',
            'date_fin' => '2027-07-31',
            'active' => true,
        ]);
        Storage::disk('local')->put('memoires/old.pdf', '%PDF-1.4 ancienne version');

        $memoire = Memoire::create([
            'titre' => 'Mémoire à corriger',
            'fichier_pdf' => 'memoires/old.pdf',
            'statut' => 'a_corriger',
            'confidentialite' => 'interne',
            'etudiant_id' => $student->id,
            'auteur_nom' => $student->name,
            'type_diplome' => 'Licence',
            'filiere_id' => $filiere->id,
            'annee_id' => $annee->id,
            'date_depot' => now()->subDay(),
        ]);
        VersionMemoire::create([
            'memoire_id' => $memoire->id,
            'fichier_pdf' => 'memoires/old.pdf',
            'numero_version' => 1,
        ]);
        Validation::create([
            'memoire_id' => $memoire->id,
            'statut' => 'a_corriger',
            'motif' => 'Ajouter la page de garde.',
            'date_decision' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('memoires.miennes'))
            ->assertOk()
            ->assertSee('Ajouter la page de garde.');

        $this->actingAs($student)
            ->post(route('memoires.resoumettre', $memoire), [
                'fichier' => UploadedFile::fake()->createWithContent('memoire-corrige.pdf', "%PDF-1.4\nversion corrigée"),
            ])
            ->assertRedirect(route('memoires.miennes'));

        $memoire->refresh();
        $this->assertSame('soumis', $memoire->statut);
        $this->assertDatabaseHas('versions_memoires', [
            'memoire_id' => $memoire->id,
            'numero_version' => 2,
        ]);
        $this->actingAs($student)
            ->get(route('memoires.miennes'))
            ->assertOk()
            ->assertSee('Historique des décisions')
            ->assertSee('Ajouter la page de garde.');
        Storage::disk('local')->assertExists('memoires/old.pdf');
        Storage::disk('local')->assertExists($memoire->fichier_pdf);
    }

    public function test_student_cannot_submit_for_an_inactive_academic_year(): void
    {
        $student = User::factory()->create(['role' => 'etudiant']);
        $filiere = Filiere::create(['nom' => 'Assurance', 'code' => 'ASS']);
        $annee = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'date_debut' => '2025-10-01',
            'date_fin' => '2026-07-31',
            'active' => false,
        ]);

        $this->actingAs($student)
            ->post(route('memoires.store'), [
                'titre' => 'Mémoire hors période',
                'type_diplome' => 'Licence',
                'filiere_id' => $filiere->id,
                'annee_id' => $annee->id,
                'fichier' => UploadedFile::fake()->createWithContent('memoire.pdf', "%PDF-1.4\ncontenu"),
            ])
            ->assertSessionHasErrors('annee_id');

        $this->assertDatabaseMissing('memoires', ['titre' => 'Mémoire hors période']);
    }

    public function test_student_cannot_access_staff_memory_management(): void
    {
        $student = User::factory()->create(['role' => 'etudiant']);

        $this->actingAs($student)
            ->get(route('memoires.index'))
            ->assertForbidden();
    }
}
