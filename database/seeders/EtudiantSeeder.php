<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtudiantSeeder extends Seeder
{
    public function run(): void
    {
        $etudiants = [
            [
                'name' => 'Awa HOUNKPE',
                'email' => 'etudiant@eneam.bj',
                'matricule' => 'ETU-2024-001',
            ],
            [
                'name' => 'Kossi ADJOVI',
                'email' => 'etudiant2@eneam.bj',
                'matricule' => 'ETU-2024-002',
            ],
        ];

        foreach ($etudiants as $etudiant) {
            User::updateOrCreate(
                ['matricule' => $etudiant['matricule']],
                [
                    'name' => $etudiant['name'],
                    'email' => $etudiant['email'],
                    'password' => Hash::make('password'),
                    'role' => 'etudiant',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
