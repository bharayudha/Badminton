<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pairing_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pairing_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->unsignedTinyInteger('team_number')->nullable();
            $table->decimal('priority_score', 8, 3)->nullable();
            $table->json('score_breakdown')->nullable();
            $table->unsignedSmallInteger('sort_position')->nullable();
            $table->timestamps();

            $table->unique(['pairing_session_id', 'player_id']);
            $table->index(['pairing_session_id', 'team_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairing_results');
    }
};
