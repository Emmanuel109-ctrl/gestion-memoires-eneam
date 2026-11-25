<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('memoires', 'couverture')) {
            Schema::table('memoires', function (Blueprint $table) {
                // Image de garde servie publiquement, jamais le PDF complet.
                $table->string('couverture')->nullable()->after('fichier_pdf');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('memoires', 'couverture')) {
            Schema::table('memoires', function (Blueprint $table) {
                $table->dropColumn('couverture');
            });
        }
    }
};
