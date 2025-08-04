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
        Schema::create("perguntas_flashcards", function (Blueprint $table) {
            $table->increments("id_pergunta");
            $table->unsignedInteger("id_flashcard");
            $table->text("pergunta");
            $table->text("resposta");
            $table->timestamps();

            $table->foreign("id_flashcard")->references("id_flashcard")->on("flashcards_independentes")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("perguntas_flashcards");
    }
};