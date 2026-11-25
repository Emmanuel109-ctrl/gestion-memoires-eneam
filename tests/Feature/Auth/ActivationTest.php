<?php

namespace Tests\Feature\Auth;

use App\Models\EtudiantEneam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class ActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function creerFicheAnnuaire(array $attributs = []): EtudiantEneam
    {
        return EtudiantEneam::create(array_merge([
            'matricule' => '20240001',
            'nom' => 'Awa HOUNKPE',
            'email' => 'awa.hounkpe@eneam.bj',
            'filiere' => 'Informatique de Gestion',
            'promotion' => '2025-2026',
            'actif' => true,
            'source' => 'test',
            'synced_at' => now(),
        ], $attributs));
    }

    public function test_activation_page_is_public_and_has_no_public_registration(): void
    {
        $this->get('/premiere-connexion')
            ->assertOk()
            ->assertSee('Activer mon compte étudiant')
            ->assertSee('Créer votre mot de passe de connexion')
            ->assertSee("vous l'utiliserez ensuite avec votre matricule", false);

        $this->get('/register')->assertNotFound();
    }

    public function test_student_present_in_the_roster_can_activate_an_account(): void
    {
        $this->creerFicheAnnuaire();

        $response = $this->post('/premiere-connexion', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'role' => 'etudiant',
            'actif' => true,
        ]);
    }

    public function test_activation_is_refused_when_email_does_not_match_the_roster(): void
    {
        $this->creerFicheAnnuaire();

        $this->post('/premiere-connexion', [
            'matricule' => '20240001',
            'email' => 'autre@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
        ])->assertSessionHasErrors('matricule');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['matricule' => '20240001']);
    }

    public function test_inactive_student_in_the_roster_cannot_activate(): void
    {
        $this->creerFicheAnnuaire(['actif' => false]);

        $this->post('/premiere-connexion', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
        ])->assertSessionHasErrors('matricule');

        $this->assertGuest();
    }

    public function test_already_activated_account_is_sent_to_login(): void
    {
        $this->creerFicheAnnuaire();

        User::factory()->create([
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
        ]);

        $this->post('/premiere-connexion', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
        ])->assertRedirect(route('login'));
    }

    public function test_activation_never_creates_a_staff_account(): void
    {
        $this->creerFicheAnnuaire();

        $this->post('/premiere-connexion', [
            'matricule' => '20240001',
            'email' => 'awa.hounkpe@eneam.bj',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'matricule' => '20240001',
            'role' => 'etudiant',
        ]);
    }

    public function test_matricule_must_have_eight_digits(): void
    {
        $this->post('/premiere-connexion', [
            'matricule' => '1234',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'attestation' => '1',
        ])->assertSessionHasErrors('matricule');
    }
}
