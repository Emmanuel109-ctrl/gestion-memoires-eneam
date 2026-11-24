<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memoire_id')->constrained('memoires')->cascadeOnDelete();

            // Un validateur supprimé ne doit pas effacer l'historique.
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('statut', ['en_verification', 'a_corriger', 'valide', 'rejete'])
                ->default('en_verification');

            $table->text('motif')->nullable();

            // Points de contrôle de la vérification documentaire.
            $table->boolean('conforme_version_papier')->default(false);
            $table->boolean('format_conforme')->default(false);

            // Renseignée uniquement au moment de la décision (valide / rejete / a_corriger).
            $table->timestamp('date_decision')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
