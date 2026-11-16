<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('experience_years', 4, 1)->unsigned()->nullable();
            $table->unsignedTinyInteger('weekly_frequency')->default(0);
            $table->text('training_club_experience')->nullable();
            $table->text('competition_experience')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_experiences');
    }
};
