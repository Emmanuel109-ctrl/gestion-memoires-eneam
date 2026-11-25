<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Ordre important : les référentiels et les utilisateurs
     * avant les mémoires (clés étrangères).
     */
    public function run(): void
    {
        $this->call([
            FiliereSeeder::class,
            AnneeAcademiqueSeeder::class,
            EtudiantEneamSeeder::class,
            AdminSeeder::class,
            BibliothecaireSeeder::class,
            EtudiantSeeder::class,
            MemoireSeeder::class,
            ValidationSeeder::class,
        ]);
    }
}
