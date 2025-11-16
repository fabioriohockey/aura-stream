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
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->integer('progress_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('watched_at')->default(now());
            $table->timestamps();

            // Índices para performance
            $table->unique(['user_id', 'episode_id']);
            $table->index(['user_id', 'watched_at']);
            $table->index(['watched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
