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
                'matricule' => '20240001',
            ],
            [
                'name' => 'Kossi ADJOVI',
                'email' => 'etudiant2@eneam.bj',
                'matricule' => '20240002',
            ],
        ];

        foreach ($etudiants as $etudiant) {
            $user = User::firstOrNew(['matricule' => $etudiant['matricule']]);

            $user->forceFill([
                'name' => $etudiant['name'],
                'email' => $etudiant['email'],
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'actif' => true,
                'email_verified_at' => now(),
            ])->save();
        }
    }
}
