<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versions_memoires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memoire_id')->constrained('memoires')->cascadeOnDelete();
            $table->string('fichier_pdf');
            $table->unsignedInteger('numero_version')->default(1);
            $table->timestamps();

            $table->unique(['memoire_id', 'numero_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions_memoires');
    }
};
