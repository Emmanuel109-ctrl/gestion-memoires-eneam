<?php

namespace Tests\Feature;

use App\Models\Filiere;
use Database\Seeders\FiliereSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiliereSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_run_repeatedly_without_duplicate_codes(): void
    {
        Filiere::create([
            'nom' => 'Ancienne Informatique',
            'code' => 'INFO',
            'description' => 'Ancienne description',
        ]);

        $this->seed(FiliereSeeder::class);
        $this->seed(FiliereSeeder::class);

        $this->assertDatabaseCount('filieres', 5);
        $this->assertDatabaseHas('filieres', [
            'nom' => 'Informatique',
            'code' => 'INFO',
            'description' => 'Filière Informatique',
        ]);
    }
}
