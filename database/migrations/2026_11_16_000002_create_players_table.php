<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('skill_level', 32)->default('belum_mengetahui');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->index('skill_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
