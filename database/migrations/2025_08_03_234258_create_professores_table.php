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
        Schema::create("professores", function (Blueprint $table) {
            $table->unsignedInteger("id_usuario")->primary(); // CHAVE PRIMÁRIA ESTRANGEIRA (UNSIGNED)
            $table->unsignedInteger("id_escola")->nullable(); // PODE SER NULL
            $table->string("titulacao", 50)->nullable(); // PODE SER NULL

            // ÍNDICES
            $table->index(["id_usuario", "id_escola"], "idx_professor_escola");

            // CHAVES ESTRANGEIRAS
            $table->foreign("id_usuario", "fk_professor_usuario")->references("id_usuario")->on("usuarios")->onDelete("cascade");
            $table->foreign("id_escola", "fk_professor_escola")->references("id_escola")->on("escolas")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("professores");
    }
};