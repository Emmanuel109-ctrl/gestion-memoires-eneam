<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BibliothecaireSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'biblio@eneam.bj'],
            [
                'name' => 'Bibliothécaire ENEAM',
                'password' => Hash::make('password'),
                'role' => 'bibliothecaire',
                'matricule' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
