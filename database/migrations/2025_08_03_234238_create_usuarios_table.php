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
        Schema::create("usuarios", function (Blueprint $table) {
            $table->increments("id_usuario"); // COLUNA AUTO_INCREMENT
            $table->primary("id_usuario"); // DEFINIÇÃO EXPLÍCITA DA CHAVE PRIMÁRIA
            $table->string("nome", 100)->nullable(false); // NOT NULL
            $table->string("email", 100)->unique()->nullable(false); // UNIQUE E NOT NULL
            $table->string("senha", 255)->nullable(false); // NOT NULL
            $table->enum("tipo", ["admin", "professor", "aluno", "independente"])->nullable(false); // NOT NULL
            $table->dateTime("data_cadastro")->useCurrent(); // DEFAULT CURRENT_TIMESTAMP
            $table->string("remember_token", 100)->nullable(); // PODE SER NULL
            $table->timestamp("updated_at")->nullable(); // PODE SER NULL

            // ÍNDICES
            $table->index("nome", "idx_usuario_nome");
            $table->index("email", "idx_usuario_email");
            $table->index(["nome", "email"], "idx_usuario_nome_email");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("usuarios");
    }
};