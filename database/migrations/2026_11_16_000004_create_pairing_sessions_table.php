<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pairing_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('mode', 32);
            $table->string('status', 20)->default('draft');
            $table->unsignedSmallInteger('participant_count')->default(0);
            $table->json('criteria_weights')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mode', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairing_sessions');
    }
};
