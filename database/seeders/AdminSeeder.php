<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@eneam.bj'],
            [
                'name' => 'Administrateur ENEAM',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'matricule' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
