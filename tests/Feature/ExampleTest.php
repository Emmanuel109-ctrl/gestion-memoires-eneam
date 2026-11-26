<?php

namespace Tests\Feature;

use Database\Seeders\FiliereSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Le site commence par une page d'accueil publique, jamais par la connexion.
     */
    public function test_guest_sees_the_public_home_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Comprendre les accès');
        $response->assertSee('Activer mon compte');
        $response->assertSee('Se connecter');
    }

    public function test_homepage_counts_all_program_filieres_not_only_filieres_with_memoires(): void
    {
        $this->seed(FiliereSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['14', "Filières de l'école"]);
    }
}
