<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\User;
use Database\Seeders\AnneeAcademiqueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnneeAcademiqueManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_academic_year_management(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('annees.index'))
            ->assertOk()
            ->assertSee('Années académiques')
            ->assertSee('Ajouter une année');
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('annees.store'), [
                'libelle' => '2026-2027',
                'date_debut' => '2027-07-31',
                'date_fin' => '2026-10-01',
            ])
            ->assertSessionHasErrors('date_fin');
    }

    public function test_first_academic_year_is_made_active_automatically(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('annees.store'), [
                'libelle' => '2026-2027',
                'date_debut' => '2026-10-01',
                'date_fin' => '2027-07-31',
            ])
            ->assertRedirect(route('annees.index'));

        $this->assertDatabaseHas('annees_academiques', [
            'libelle' => '2026-2027',
            'active' => true,
        ]);
    }

    public function test_activating_an_academic_year_deactivates_the_previous_one(): void
    {
        $admin = User::factory()->admin()->create();
        AnneeAcademique::create([
            'libelle' => '2025-2026',
            'date_debut' => '2025-10-01',
            'date_fin' => '2026-07-31',
            'active' => true,
        ]);
        $nextYear = AnneeAcademique::create([
            'libelle' => '2026-2027',
            'date_debut' => '2026-10-01',
            'date_fin' => '2027-07-31',
            'active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('annees.activate', $nextYear))
            ->assertRedirect(route('annees.index'));

        $this->assertDatabaseHas('annees_academiques', [
            'libelle' => '2025-2026',
            'active' => false,
        ]);
        $this->assertDatabaseHas('annees_academiques', [
            'libelle' => '2026-2027',
            'active' => true,
        ]);
    }

    public function test_seeder_leaves_only_one_academic_year_active(): void
    {
        AnneeAcademique::create([
            'libelle' => '2030-2031',
            'date_debut' => '2030-10-01',
            'date_fin' => '2031-07-31',
            'active' => true,
        ]);

        $this->seed(AnneeAcademiqueSeeder::class);

        $this->assertSame(1, AnneeAcademique::where('active', true)->count());
        $this->assertDatabaseHas('annees_academiques', [
            'libelle' => '2026-2027',
            'active' => true,
        ]);
    }
}
