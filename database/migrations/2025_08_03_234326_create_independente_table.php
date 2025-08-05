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
        Schema::create("independente", function (Blueprint $table) {
            $table->unsignedInteger("id_usuario")->primary(); // CHAVE PRIMÁRIA ESTRANGEIRA (UNSIGNED)

            // CHAVES ESTRANGEIRAS
            $table->foreign("id_usuario", "fk_independente_usuario")->references("id_usuario")->on("usuario")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("independente");
    }
};