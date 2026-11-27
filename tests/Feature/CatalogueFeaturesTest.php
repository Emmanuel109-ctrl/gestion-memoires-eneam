<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreeDonneesMemoires;
use Tests\TestCase;

class CatalogueFeaturesTest extends TestCase
{
    use CreeDonneesMemoires;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('memoires/test.pdf', '%PDF-1.4 contenu de test');
    }

    public function test_catalogue_filters_by_diploma_type(): void
    {
        $this->creerMemoire(['titre' => 'Mémoire Licence', 'type_diplome' => 'Licence']);
        $this->creerMemoire(['titre' => 'Mémoire Master', 'type_diplome' => 'Master']);

        $this->get('/catalogue?type_diplome=Master')
            ->assertOk()
            ->assertSee('Mémoire Master')
            ->assertDontSee('Mémoire Licence');
    }

    public function test_catalogue_can_sort_by_title(): void
    {
        $this->creerMemoire(['titre' => 'Mémoire Bravo']);
        $this->creerMemoire(['titre' => 'Mémoire Alpha']);

        $this->get('/catalogue?tri=titre')
            ->assertOk()
            ->assertSeeInOrder(['Mémoire Alpha', 'Mémoire Bravo']);
    }

    public function test_authenticated_user_can_toggle_a_favorite(): void
    {
        $etudiant = User::factory()->create();
        $memoire = $this->creerMemoire(['titre' => 'Mémoire favori']);

        $this->actingAs($etudiant)
            ->post(route('favoris.toggle', $memoire))
            ->assertRedirect();

        $this->assertDatabaseHas('favoris', [
            'user_id' => $etudiant->id,
            'memoire_id' => $memoire->id,
        ]);

        $this->actingAs($etudiant)
            ->get(route('favoris.index'))
            ->assertOk()
            ->assertSee('Mes favoris')
            ->assertSee('Mémoire favori');

        $this->actingAs($etudiant)
            ->post(route('favoris.toggle', $memoire))
            ->assertRedirect();

        $this->assertDatabaseMissing('favoris', [
            'user_id' => $etudiant->id,
            'memoire_id' => $memoire->id,
        ]);
    }

    public function test_guest_cannot_add_a_favorite(): void
    {
        $memoire = $this->creerMemoire();

        $this->post(route('favoris.toggle', $memoire))->assertRedirect('/login');
    }

    public function test_memoir_notice_exposes_favorites_and_reader(): void
    {
        $etudiant = User::factory()->create();
        $memoire = $this->creerMemoire();

        $this->actingAs($etudiant)
            ->get(route('catalogue.show', $memoire))
            ->assertOk()
            ->assertSee('Ajouter aux favoris')
            ->assertSee('Lire en ligne');
    }

    public function test_integrated_reader_requires_an_authorized_account(): void
    {
        $etudiant = User::factory()->create();
        $memoire = $this->creerMemoire();

        $this->get(route('catalogue.lecteur', $memoire))->assertRedirect('/login');

        $this->actingAs($etudiant)
            ->get(route('catalogue.lecteur', $memoire))
            ->assertOk()
            ->assertSee('iframe', false)
            ->assertSee(route('catalogue.lire', $memoire), false);
    }

    public function test_integrated_reader_respects_restricted_access(): void
    {
        $etudiant = User::factory()->create();
        $restreint = $this->creerMemoire(['confidentialite' => 'restreint']);

        $this->actingAs($etudiant)
            ->get(route('catalogue.lecteur', $restreint))
            ->assertForbidden();
    }
}
