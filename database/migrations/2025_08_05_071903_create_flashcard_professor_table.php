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
        
        Schema::create("flashcard_professor", function (Blueprint $table) {
            $table->increments("id_flashcard_professor");
            $table->unsignedInteger("id_topico_professor");
            $table->string("titulo", 255);
            $table->text("descricao")->nullable();
            $table->dateTime("data_criacao")->useCurrent();
            $table->timestamps();

            $table->foreign("id_topico_professor")->references("id_topico_professor")->on("topico_professor")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("flashcard_professor");
    }
};