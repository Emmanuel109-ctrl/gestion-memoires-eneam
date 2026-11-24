<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historiques_lecture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('memoire_id')->constrained('memoires')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['memoire_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiques_lecture');
    }
};
