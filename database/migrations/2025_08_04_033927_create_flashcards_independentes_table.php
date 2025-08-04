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
        Schema::create("flashcards_independentes", function (Blueprint $table) {
            $table->increments("id_flashcard");
            $table->unsignedInteger("id_topico");
            $table->string("titulo", 255);
            $table->text("descricao")->nullable();
            $table->dateTime("data_criacao")->useCurrent();
            $table->timestamps();

            $table->foreign("id_topico")->references("id_topico")->on("topicos_independentes")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("flashcards_independentes");
    }
};