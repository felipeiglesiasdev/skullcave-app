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
        Schema::create("interesse_escolas", function (Blueprint $table) {
            $table->id();
            $table->string("nome", 255)->comment("Nome do responsável");
            $table->string("email", 255)->comment("Email do responsável");
            $table->string("cargo", 255)->comment("Cargo que ocupa na escola");
            $table->string("nome_escola", 255)->comment("Nome da escola/instituição");
            $table->string("cnpj", 18)->comment("CNPJ da escola");
            $table->string("telefone", 20)->comment("Telefone de contato");
            $table->timestamp("data_interesse")->useCurrent()->comment("Data do registro de interesse");
            $table->timestamps();

            // Índices
            $table->index("email", "idx_interesse_escolas_email");
            $table->index("cnpj", "idx_interesse_escolas_cnpj");
            $table->index("data_interesse", "idx_interesse_escolas_data_interesse");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("interesse_escolas");
    }
};