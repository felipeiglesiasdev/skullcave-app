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
        Schema::create("professor", function (Blueprint $table) {
            $table->unsignedInteger("id_usuario")->primary(); // CHAVE PRIMÁRIA ESTRANGEIRA (UNSIGNED)
            $table->unsignedInteger("id_escola")->nullable(); // PODE SER NULL
            $table->string("titulacao", 50)->nullable(); // PODE SER NULL


            // CHAVES ESTRANGEIRAS
            $table->foreign("id_usuario", "fk_professor_usuario")->references("id_usuario")->on("usuario")->onDelete("cascade");
            $table->foreign("id_escola", "fk_professor_escola")->references("id_escola")->on("escola")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("professor");
    }
};