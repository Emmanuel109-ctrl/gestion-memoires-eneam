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

    public function test_new_students_can_register_with_their_matricule(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Étudiant',
            'matricule' => 'ETU-2024-999',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'matricule' => 'ETU-2024-999',
            'role' => 'etudiant',
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
        User::factory()->create(['matricule' => 'ETU-2024-001']);

        $this->post('/register', [
            'name' => 'Doublon',
            'matricule' => 'ETU-2024-001',
            'email' => 'doublon@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('matricule');
    }
}
