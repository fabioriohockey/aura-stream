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
        Schema::create('doramas', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description');
            $table->text('synopsis')->nullable();
            $table->string('country', 50); // Coreia, Japão, China, Tailândia
            $table->year('year');
            $table->integer('episodes_total');
            $table->integer('duration_minutes')->default(45); // Duração média dos episódios
            $table->string('poster_path', 500); // Caminho do arquivo
            $table->string('backdrop_path', 500)->nullable(); // Caminho do arquivo
            $table->string('trailer_url', 500)->nullable(); // YouTube ou local
            $table->enum('status', ['em_exibicao', 'finalizado', 'cancelado'])->default('finalizado');
            $table->decimal('rating', 3, 2)->default(0.00); // Avaliação 0.00-10.00
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('release_date')->nullable();
            $table->string('language', 10)->default('ko'); // ko, ja, zh, th
            $table->json('genres')->nullable(); // Array de categorias
            $table->string('imdb_id', 20)->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['country', 'year']);
            $table->index(['is_featured', 'is_active']);
            $table->index(['rating']);
            $table->index(['views_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doramas');
    }
};
