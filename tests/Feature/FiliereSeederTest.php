<?php

namespace Tests\Feature;

use App\Models\Filiere;
use Database\Seeders\FiliereSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeDonneesMemoires;
use Tests\TestCase;

class FiliereSeederTest extends TestCase
{
    use CreeDonneesMemoires;
    use RefreshDatabase;

    public function test_it_can_be_run_repeatedly_without_duplicate_codes(): void
    {
        Filiere::create([
            'nom' => 'Ancienne Informatique',
            'code' => 'INFO',
            'description' => 'Ancienne description',
        ]);

        $ancienneAip = Filiere::create([
            'nom' => 'Analyse et Programmation',
            'code' => 'AIP',
        ]);
        $memoire = $this->creerMemoire(['filiere_id' => $ancienneAip->id]);

        foreach ([
            ['nom' => 'Ancienne gestion', 'code' => 'GEST'],
            ['nom' => 'Ancienne agronomie', 'code' => 'AGRO'],
            ['nom' => 'Ancienne économie', 'code' => 'ECO'],
            ['nom' => 'Génie Civil', 'code' => 'GENCIV'],
            ['nom' => 'Administration des Réseaux', 'code' => 'ARI'],
        ] as $ancienneFiliere) {
            Filiere::create($ancienneFiliere);
        }

        $this->seed(FiliereSeeder::class);
        $this->seed(FiliereSeeder::class);

        $this->assertDatabaseCount('filieres', 14);
        $this->assertDatabaseHas('filieres', [
            'nom' => 'Informatique de gestion',
            'code' => 'INFO',
            'description' => 'Filière de Licence (Bac+3)',
        ]);

        $filieresAttendues = [
            'INFO' => 'Informatique de gestion',
            'ASS' => 'Assurance',
            'BFM' => 'Banque et Finances de Marché (BFM)',
            'BIMF' => 'Banque et Institutions de Microfinance (BIMF)',
            'DLR' => 'Développement Local et Régional (DLR)',
            'EGE' => 'Économie et Gestion de l’Environnement (EGE)',
            'GC' => 'Gestion Commerciale (GC)',
            'GRH' => 'Gestion des Ressources Humaines (GRH)',
            'GTL' => 'Gestion des Transports et Logistique (GTL)',
            'GFC' => 'Gestion Financière et Comptable (GFC)',
            'PGP' => 'Planification et Gestion des Projets (PGP)',
            'SDS' => 'Statistique Démographique et Sociale (SDS)',
            'SES' => 'Statistique Économique et Sectorielle (SES)',
            'DECOGEF' => 'Diplôme d’Études de Comptabilité et de Gestion Financière (DECOGEF)',
        ];

        foreach ($filieresAttendues as $code => $nom) {
            $this->assertDatabaseHas('filieres', compact('code', 'nom'));
        }

        foreach (['GEST', 'AGRO', 'ECO', 'GENCIV', 'AIP', 'ARI'] as $ancienCode) {
            $this->assertDatabaseMissing('filieres', ['code' => $ancienCode]);
        }

        $this->assertDatabaseHas('memoires', [
            'id' => $memoire->id,
            'filiere_id' => Filiere::where('code', 'INFO')->value('id'),
        ]);
    }
}
