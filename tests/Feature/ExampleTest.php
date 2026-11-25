<?php

namespace Tests\Feature;

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
        $response->assertSee('Explorer le catalogue');
        $response->assertSee('Se connecter');
    }
}
