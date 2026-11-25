<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_student_can_register_with_an_eight_digit_matricule_and_personal_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Étudiant',
            'matricule' => '00123456',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'matricule' => '00123456',
            'role' => 'etudiant',
        ]);
    }

    public function test_registration_cannot_escalate_to_an_administrator_role(): void
    {
        $this->post('/register', [
            'name' => 'Étudiant curieux',
            'matricule' => '20240077',
            'email' => 'curieux@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'curieux@example.com',
            'role' => 'etudiant',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'curieux@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_matricule_is_required(): void
    {
        $this->post('/register', [
            'name' => 'Sans Matricule',
            'email' => 'sans@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('matricule');
    }

    public function test_matricule_must_be_unique(): void
    {
        User::factory()->create(['matricule' => '20240001']);

        $this->post('/register', [
            'name' => 'Doublon',
            'matricule' => '20240001',
            'email' => 'doublon@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('matricule');
    }

    public function test_matricule_must_contain_exactly_eight_digits(): void
    {
        foreach (['1234567', '123456789', '1234ABCD'] as $index => $matricule) {
            $this->post('/register', [
                'name' => 'Matricule invalide',
                'matricule' => $matricule,
                'email' => "invalid{$index}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
            ])->assertSessionHasErrors('matricule');
        }
    }
}
