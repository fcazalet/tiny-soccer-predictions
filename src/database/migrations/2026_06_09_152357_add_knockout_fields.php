<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter le vainqueur réel sur le match (déterminé après prolongations/TAB)
        Schema::table('fixtures', function (Blueprint $table) {
            $table->enum('winner', ['home', 'away'])->nullable()->after('away_score');
        });

        // Ajouter le vainqueur prédit sur le pronostic
        Schema::table('predictions', function (Blueprint $table) {
            $table->enum('predicted_winner', ['home', 'away'])->nullable()->after('away_score');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn('winner');
        });

        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn('predicted_winner');
        });
    }
};
