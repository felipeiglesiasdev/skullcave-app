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
        Schema::create("turma_aluno", function (Blueprint $table) {
            $table->increments("id_turma_aluno");
            $table->unsignedInteger("id_turma");
            $table->unsignedInteger("id_usuario_aluno");
            $table->timestamps();

            $table->foreign("id_turma")->references("id_turma")->on("turma")->onDelete("cascade");
            $table->foreign("id_usuario_aluno")->references("id_usuario")->on("aluno")->onDelete("cascade");

            // Evitar duplicação de aluno na mesma turma
            $table->unique(["id_turma", "id_usuario_aluno"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("turma_aluno");
    }
};