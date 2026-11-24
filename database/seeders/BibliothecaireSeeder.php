<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BibliothecaireSeeder extends Seeder
{
    public function run(): void
    {
        $biblio = User::firstOrNew(['email' => 'biblio@eneam.bj']);

        $biblio->forceFill([
            'name' => 'Bibliothécaire ENEAM',
            'password' => Hash::make('password'),
            'role' => 'bibliothecaire',
            'matricule' => null,
            'actif' => true,
            'email_verified_at' => now(),
        ])->save();
    }
}
