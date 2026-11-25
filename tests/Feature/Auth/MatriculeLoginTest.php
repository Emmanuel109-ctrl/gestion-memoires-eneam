<?php

namespace Tests\Feature\Auth;

use App\Models\EtudiantEneam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatriculeLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_log_in_with_matricule(): void
    {
        $etudiant = User::factory()->create([
            'matricule' => '20240001',
        ]);

        $response = $this->post('/login', [
            'identifiant' => '20240001',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($etudiant);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_staff_can_still_log_in_with_email(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@eneam.bj',
        ]);

        $response = $this->post('/login', [
            'identifiant' => 'admin@eneam.bj',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_login_fails_with_unknown_matricule(): void
    {
        $this->post('/login', [
            'identifiant' => 'INCONNU-001',
            'password' => 'password',
        ])->assertSessionHasErrors('identifiant');

        $this->assertGuest();
    }

    public function test_student_removed_from_the_roster_cannot_log_in(): void
    {
        $etudiant = User::factory()->create([
            'matricule' => '20240001',
        ]);

        EtudiantEneam::create([
            'matricule' => '20240001',
            'nom' => $etudiant->name,
            'email' => $etudiant->email,
            'actif' => false,
        ]);

        $this->post('/login', [
            'identifiant' => '20240001',
            'password' => 'password',
        ])->assertSessionHasErrors('identifiant');

        $this->assertGuest();
    }
}
