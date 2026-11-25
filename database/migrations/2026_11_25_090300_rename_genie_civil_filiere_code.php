<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('filieres')
            ->where('code', 'GC')
            ->where('nom', 'Génie Civil')
            ->update(['code' => 'GENCIV']);
    }

    public function down(): void
    {
        if (DB::table('filieres')->where('code', 'GC')->exists()) {
            return;
        }

        DB::table('filieres')
            ->where('code', 'GENCIV')
            ->where('nom', 'Génie Civil')
            ->update(['code' => 'GC']);
    }
};
