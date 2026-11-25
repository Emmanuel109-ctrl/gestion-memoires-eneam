<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Annuaire officiel des étudiants ENEAM.
 *
 * Cette table est séparée de `users` : elle contient les personnes
 * autorisées à activer un compte, pas les comptes eux-mêmes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etudiants_eneam', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 8)->unique();
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('filiere')->nullable();
            $table->string('promotion')->nullable();
            $table->boolean('actif')->default(true);
            $table->string('source')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudiants_eneam');
    }
};
