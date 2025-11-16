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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dorama_id')->constrained()->onDelete('cascade');
            $table->integer('episode_number');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('video_path_480p', 500); // Caminho do vídeo 480p
            $table->string('video_path_720p', 500)->nullable(); // Caminho do vídeo 720p (premium)
            $table->string('thumbnail_path', 500)->nullable(); // Thumbnail do episódio
            $table->string('subtitles_path', 500)->nullable(); // Caminho das legendas
            $table->integer('duration_seconds')->default(0);
            $table->decimal('file_size_480p_mb', 8, 2)->default(0); // Tamanho em MB
            $table->decimal('file_size_720p_mb', 8, 2)->nullable(); // Tamanho em MB
            $table->string('video_format', 10)->default('webm'); // webm, mp4
            $table->string('video_codec', 10)->default('h265'); // h264, h265
            $table->integer('views_count')->default(0);
            $table->boolean('is_premium_only')->default(false); // Só para premium
            $table->boolean('is_active')->default(true);
            $table->timestamp('air_date')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->unique(['dorama_id', 'episode_number']);
            $table->index(['dorama_id', 'is_active']);
            $table->index(['is_premium_only']);
            $table->index(['views_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
