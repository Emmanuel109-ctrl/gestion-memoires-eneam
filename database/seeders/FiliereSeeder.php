<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            ['nom' => 'Informatique', 'code' => 'INFO', 'description' => 'Filière Informatique'],
            ['nom' => 'Gestion', 'code' => 'GEST', 'description' => 'Filière Gestion'],
            ['nom' => 'Agronomie', 'code' => 'AGRO', 'description' => 'Filière Agronomie'],
            ['nom' => 'Économie', 'code' => 'ECO', 'description' => 'Filière Économie'],
            ['nom' => 'Génie Civil', 'code' => 'GC', 'description' => 'Filière Génie Civil'],
        ];

        Filiere::upsert($filieres, ['code'], ['nom', 'description']);
    }
}
