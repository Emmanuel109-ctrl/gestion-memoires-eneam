<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('historiques_lecture', 'type')) {
            Schema::table('historiques_lecture', function (Blueprint $table) {
                // « lecture » ou « telechargement » : trace les accès au texte intégral.
                $table->string('type', 20)->default('lecture')->after('memoire_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('historiques_lecture', 'type')) {
            Schema::table('historiques_lecture', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
