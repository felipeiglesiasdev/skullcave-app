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
        Schema::create("escolas", function (Blueprint $table) {
            $table->increments("id_escola"); // CHAVE PRIMÁRIA AUTO_INCREMENT
            $table->primary("id_escola"); // DEFINIÇÃO EXPLÍCITA DA CHAVE PRIMÁRIA
            $table->string("nome", 100)->nullable(false); // NOT NULL
            $table->string("cnpj", 18)->unique()->nullable(false); // UNIQUE E NOT NULL
            $table->text("endereco")->nullable(); // PODE SER NULL
            $table->string("telefone", 20)->nullable(); // PODE SER NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("escolas");
    }
};