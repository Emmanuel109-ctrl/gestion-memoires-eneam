<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreeDonneesMemoires;
use Tests\TestCase;

class CataloguePublicTest extends TestCase
{
    use CreeDonneesMemoires;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::disk('local')->put('memoires/test.pdf', '%PDF-1.4 contenu de test');
    }

    public function test_guest_can_browse_the_public_catalogue(): void
    {
        $public = $this->creerMemoire(['titre' => 'Mémoire public']);
        $this->creerMemoire(['titre' => 'Mémoire interne', 'confidentialite' => 'interne']);
        $this->creerMemoire(['titre' => 'Mémoire restreint', 'confidentialite' => 'restreint']);
        $this->creerMemoire(['titre' => 'Mémoire brouillon', 'statut' => 'soumis']);

        $this->get('/catalogue')
            ->assertOk()
            ->assertSee('Mémoire public')
            ->assertDontSee('Mémoire interne')
            ->assertDontSee('Mémoire restreint')
            ->assertDontSee('Mémoire brouillon');

        $this->get(route('catalogue.show', $public))
            ->assertOk()
            ->assertSee('Mémoire public')
            ->assertSee('Se connecter');
    }

    public function test_guest_cannot_view_a_non_public_notice(): void
    {
        $interne = $this->creerMemoire(['confidentialite' => 'interne']);

        $this->get(route('catalogue.show', $interne))->assertForbidden();
    }

    public function test_cover_is_public_and_never_exposes_the_pdf(): void
    {
        $memoire = $this->creerMemoire();

        $this->get(route('catalogue.couverture', $memoire))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');

        $this->get(route('catalogue.lire', $memoire))->assertRedirect('/login');
    }

    public function test_student_can_read_public_and_internal_memoirs(): void
    {
        $etudiant = User::factory()->create(['matricule' => '20240001']);
        $public = $this->creerMemoire(['titre' => 'Public']);
        $interne = $this->creerMemoire(['titre' => 'Interne', 'confidentialite' => 'interne']);

        $this->actingAs($etudiant);

        $this->get(route('catalogue.lire', $public))->assertOk();
        $this->get(route('catalogue.lire', $interne))->assertOk();

        $this->assertDatabaseHas('historiques_lecture', [
            'user_id' => $etudiant->id,
            'memoire_id' => $public->id,
            'type' => 'lecture',
        ]);
    }

    public function test_student_cannot_read_a_restricted_memoir(): void
    {
        $etudiant = User::factory()->create(['matricule' => '20240001']);
        $restreint = $this->creerMemoire(['confidentialite' => 'restreint']);

        $this->actingAs($etudiant)
            ->get(route('catalogue.lire', $restreint))
            ->assertForbidden();
    }

    public function test_embargo_blocks_the_full_text_until_the_release_date(): void
    {
        $etudiant = User::factory()->create(['matricule' => '20240001']);
        $biblio = User::factory()->bibliothecaire()->create();

        $sousEmbargo = $this->creerMemoire([
            'date_embargo' => now()->addMonth()->toDateString(),
        ]);

        $this->actingAs($etudiant)
            ->get(route('catalogue.lire', $sousEmbargo))
            ->assertForbidden();

        $this->actingAs($biblio)
            ->get(route('catalogue.lire', $sousEmbargo))
            ->assertOk();
    }

    public function test_download_respects_the_authorization_flag(): void
    {
        $etudiant = User::factory()->create(['matricule' => '20240001']);

        $interdit = $this->creerMemoire(['telechargement_autorise' => false]);
        $autorise = $this->creerMemoire([
            'titre' => 'Téléchargeable',
            'telechargement_autorise' => true,
        ]);

        $this->actingAs($etudiant);

        $this->get(route('catalogue.telecharger', $interdit))->assertForbidden();
        $this->get(route('catalogue.telecharger', $autorise))->assertOk();

        $this->assertDatabaseHas('historiques_lecture', [
            'user_id' => $etudiant->id,
            'memoire_id' => $autorise->id,
            'type' => 'telechargement',
        ]);
    }

    public function test_staff_can_read_every_memoir_including_restricted_ones(): void
    {
        $biblio = User::factory()->bibliothecaire()->create();
        $restreint = $this->creerMemoire([
            'confidentialite' => 'restreint',
            'telechargement_autorise' => false,
        ]);

        $this->actingAs($biblio);

        $this->get(route('catalogue.lire', $restreint))->assertOk();
        $this->get(route('catalogue.telecharger', $restreint))->assertOk();
    }

    public function test_catalogue_search_filters_by_title(): void
    {
        $this->creerMemoire(['titre' => 'Analyse prédictive des navires']);
        $this->creerMemoire(['titre' => 'Gestion des rendez-vous']);

        $this->get('/catalogue?q=navires')
            ->assertOk()
            ->assertSee('Analyse prédictive des navires')
            ->assertDontSee('Gestion des rendez-vous');
    }
}
