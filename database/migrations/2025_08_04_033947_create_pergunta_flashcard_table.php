<?php
use Illuminate\Database\Eloquent\Factories\HasFactory; 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    use HasFactory;
    public function up(): void
    {
        Schema::create("pergunta_flashcard", function (Blueprint $table) {
            $table->increments("id_pergunta");
            $table->unsignedInteger("id_flashcard");
            $table->text("pergunta");
            $table->text("resposta");
            $table->timestamps();

            $table->foreign("id_flashcard")->references("id_flashcard")->on("flashcard")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("pergunta_flashcard");
    }
};