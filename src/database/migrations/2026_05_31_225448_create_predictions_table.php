<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('fixture_id')->constrained('fixtures')->cascadeOnDelete();
            $table->unsignedTinyInteger('home_score');
            $table->unsignedTinyInteger('away_score');
            $table->unsignedTinyInteger('points_earned')->nullable(); // null = match pas encore joué
            $table->timestamps();

            $table->unique(['user_id', 'fixture_id']); // un seul pronostic par match par joueur
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
