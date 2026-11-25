<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_see_the_public_home_and_are_redirected_from_dashboard(): void
    {
        $this->get('/')->assertOk();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_sees_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tableau de bord — Administration')
            ->assertSee('Répartition par statut');
    }

    public function test_bibliothecaire_sees_the_library_dashboard(): void
    {
        $biblio = User::factory()->bibliothecaire()->create();

        $this->actingAs($biblio)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tableau de bord — Bibliothèque')
            ->assertSee('File de vérification');
    }

    public function test_etudiant_sees_his_own_dashboard(): void
    {
        $etudiant = User::factory()->create([
            'role' => 'etudiant',
            'matricule' => '20240001',
        ]);

        $this->actingAs($etudiant)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Mon espace étudiant')
            ->assertSee('20240001');
    }

    public function test_authenticated_user_sees_the_public_home_page(): void
    {
        $user = User::factory()->create(['matricule' => '20240003']);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Mon espace');
    }
}
