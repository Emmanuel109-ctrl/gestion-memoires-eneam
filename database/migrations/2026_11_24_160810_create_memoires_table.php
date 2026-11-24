<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memoires', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('resume')->nullable();
            $table->text('mots_cles')->nullable();
            $table->string('fichier_pdf');
            $table->string('fichier_hash', 64)->nullable()->unique();
            $table->enum('statut', ['soumis', 'en_verification', 'valide', 'rejete'])->default('soumis');
            $table->enum('confidentialite', ['public', 'interne', 'restreint'])->default('public');
            $table->boolean('telechargement_autorise')->default(true);
            $table->date('date_embargo')->nullable();
            $table->foreignId('etudiant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auteur_nom')->nullable();
            $table->string('type_diplome')->nullable();
            $table->date('date_soutenance')->nullable();
            $table->string('cote')->nullable();
            $table->foreignId('filiere_id')->constrained('filieres')->restrictOnDelete();
            $table->foreignId('annee_id')->constrained('annees_academiques')->restrictOnDelete();
            $table->boolean('admin_depose')->default(false);
            $table->integer('vues')->default(0);
            $table->timestamp('date_depot')->useCurrent();
            $table->timestamps();

            $table->index('statut');
            $table->index('date_depot');

            // FULLTEXT n'est supporté que par MySQL / MariaDB (pas SQLite).
            if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
                $table->fullText(['titre', 'resume', 'mots_cles']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memoires');
    }
};
