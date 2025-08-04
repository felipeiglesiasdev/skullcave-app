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
        Schema::create("topicos_independentes", function (Blueprint $table) {
            $table->increments("id_topico");
            $table->unsignedInteger("id_disciplina");
            $table->string("nome", 255);
            $table->text("descricao")->nullable();
            $table->timestamps();

            $table->foreign("id_disciplina")->references("id_disciplina")->on("disciplinas_independentes")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("topicos_independentes");
    }
};