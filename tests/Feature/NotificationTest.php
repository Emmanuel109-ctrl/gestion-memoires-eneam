<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\DecisionMemoireNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreeDonneesMemoires;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use CreeDonneesMemoires;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('memoires/test.pdf', '%PDF-1.4 contenu de test');
    }

    public function test_a_validation_decision_notifies_the_author(): void
    {
        $auteur = User::factory()->create();
        $bibliothecaire = User::factory()->bibliothecaire()->create();
        $memoire = $this->creerMemoire([
            'statut' => 'en_verification',
            'etudiant_id' => $auteur->id,
        ]);

        $this->actingAs($bibliothecaire)
            ->patch(route('validations.update', $memoire), [
                'statut' => 'a_corriger',
                'motif' => 'La page de garde est incomplète.',
            ])
            ->assertRedirect(route('validations.index'));

        $this->assertSame(1, $auteur->notifications()->count());

        $notification = $auteur->notifications()->first();

        $this->assertSame('a_corriger', $notification->data['statut']);
        $this->assertSame('La page de garde est incomplète.', $notification->data['motif']);
        $this->assertNull($notification->read_at);
    }

    public function test_a_new_deposit_notifies_the_active_staff_only(): void
    {
        $etudiant = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $bibliothecaire = User::factory()->bibliothecaire()->create();
        $inactif = User::factory()->bibliothecaire()->inactif()->create();

        $this->actingAs($etudiant)
            ->post(route('memoires.store'), [
                'titre' => 'Mémoire de test notifié',
                'resume' => 'Résumé de démonstration.',
                'mots_cles' => 'test, notification',
                'type_diplome' => 'Licence',
                'filiere_id' => $this->creerFiliere()->id,
                'annee_id' => $this->creerAnnee()->id,
                'fichier' => UploadedFile::fake()->create('memoire.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect(route('memoires.miennes'));

        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(1, $bibliothecaire->notifications()->count());
        $this->assertSame(0, $inactif->notifications()->count());
    }

    public function test_the_user_can_read_a_notification_and_mark_all_as_read(): void
    {
        $etudiant = User::factory()->create();
        $memoire = $this->creerMemoire(['etudiant_id' => $etudiant->id]);

        $etudiant->notify(new DecisionMemoireNotification($memoire, 'valide'));

        $notification = $etudiant->notifications()->firstOrFail();

        $this->actingAs($etudiant)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Mémoire validé')
            ->assertSee($memoire->titre);

        $this->actingAs($etudiant)
            ->post(route('notifications.lire', $notification->id))
            ->assertRedirect(route('memoires.miennes'));

        $this->assertNotNull($notification->fresh()->read_at);

        $etudiant->notify(new DecisionMemoireNotification($memoire, 'rejete', 'Motif de test.'));

        $this->actingAs($etudiant)
            ->post(route('notifications.toutLire'))
            ->assertRedirect(route('notifications.index'));

        $this->assertSame(0, $etudiant->unreadNotifications()->count());
    }

    public function test_guests_cannot_access_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect('/login');
    }
}
