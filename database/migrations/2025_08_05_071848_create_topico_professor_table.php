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
        Schema::create("topico_professor", function (Blueprint $table) {
            $table->increments("id_topico_professor");
            $table->unsignedInteger("id_disciplina_professor");
            $table->string("nome", 255);
            $table->text("descricao")->nullable();
            $table->timestamps();

            $table->foreign("id_disciplina_professor")->references("id_disciplina_professor")->on("disciplina_professor")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("topico_professor");
    }
};