<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_bibliothecaire_can_open_the_queue_and_examination_form(): void
    {
        $bibliothecaire = User::factory()->bibliothecaire()->create();
        $memoire = $this->createMemoire();

        $this->actingAs($bibliothecaire)
            ->get(route('validations.index'))
            ->assertOk()
            ->assertSee('File de validation')
            ->assertSee('Examiner');

        $this->get(route('validations.show', $memoire))
            ->assertOk()
            ->assertSee('Contrôle de conformité')
            ->assertSee('Commentaire / motif');
    }

    public function test_bibliothecaire_can_record_a_decision_with_required_reason(): void
    {
        $bibliothecaire = User::factory()->bibliothecaire()->create();
        $memoire = $this->createMemoire();

        $this->actingAs($bibliothecaire)
            ->patch(route('validations.update', $memoire), [
                'statut' => 'rejete',
                'conforme_version_papier' => '1',
            ])
            ->assertSessionHasErrors('motif');

        $this->assertDatabaseHas('memoires', [
            'id' => $memoire->id,
            'statut' => 'en_verification',
        ]);
        $this->assertDatabaseMissing('validations', ['memoire_id' => $memoire->id]);

        $this->actingAs($bibliothecaire)
            ->patch(route('validations.update', $memoire), [
                'statut' => 'rejete',
                'motif' => 'Le PDF ne correspond pas à la version papier.',
                'conforme_version_papier' => '1',
            ])
            ->assertRedirect(route('validations.index'));

        $validation = Validation::where('memoire_id', $memoire->id)->firstOrFail();

        $this->assertSame('rejete', $validation->statut);
        $this->assertSame($bibliothecaire->id, $validation->validateur_id);
        $this->assertSame('Le PDF ne correspond pas à la version papier.', $validation->motif);
        $this->assertTrue($validation->conforme_version_papier);
        $this->assertFalse($validation->format_conforme);
        $this->assertNotNull($validation->date_decision);
        $this->assertDatabaseHas('memoires', [
            'id' => $memoire->id,
            'statut' => 'rejete',
        ]);
    }

    public function test_students_cannot_access_the_validation_queue(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('validations.index'))
            ->assertForbidden();
    }

    private function createMemoire(): Memoire
    {
        $filiere = Filiere::create([
            'nom' => 'Informatique de gestion',
            'code' => 'INFO',
            'description' => 'Filière de Licence (Bac+3)',
        ]);
        $annee = AnneeAcademique::create([
            'libelle' => '2026-2027',
            'date_debut' => '2026-10-01',
            'date_fin' => '2027-07-31',
            'active' => true,
        ]);

        return Memoire::create([
            'titre' => 'Mémoire de test',
            'fichier_pdf' => 'memoires/test.pdf',
            'filiere_id' => $filiere->id,
            'annee_id' => $annee->id,
            'statut' => 'en_verification',
        ]);
    }
}
