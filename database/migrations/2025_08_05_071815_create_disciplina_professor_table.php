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
        Schema::create("disciplina_professor", function (Blueprint $table) {
            $table->increments("id_disciplina_professor");
            $table->unsignedInteger("id_turma");
            $table->unsignedInteger("id_professor");
            $table->string("nome", 255);
            $table->text("descricao")->nullable();
            $table->timestamps();

            $table->foreign("id_turma")->references("id_turma")->on("turma")->onDelete("cascade");
            $table->foreign("id_professor")->references("id_usuario")->on("professor")->onDelete("cascade");

            // Evitar duplicação de disciplina do professor na mesma turma
            $table->unique(["id_turma", "id_professor", "nome"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("disciplina_professor");
    }
};