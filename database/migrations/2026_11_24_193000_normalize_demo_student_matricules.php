<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MATRICULES = [
        'ETU-2024-001' => '20240001',
        'ETU-2024-002' => '20240002',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            foreach (self::MATRICULES as $ancien => $nouveau) {
                $id = DB::table('users')->where('matricule', $ancien)->value('id');

                if ($id !== null && ! DB::table('users')->where('matricule', $nouveau)->exists()) {
                    DB::table('users')->where('id', $id)->update(['matricule' => $nouveau]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            foreach (self::MATRICULES as $ancien => $nouveau) {
                $id = DB::table('users')->where('matricule', $nouveau)->value('id');

                if ($id !== null && ! DB::table('users')->where('matricule', $ancien)->exists()) {
                    DB::table('users')->where('id', $id)->update(['matricule' => $ancien]);
                }
            }
        });
    }
};
