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
        Schema::create("disciplina", function (Blueprint $table) {
            $table->increments("id_disciplina");
            $table->unsignedInteger("id_usuario");
            $table->string("nome", 255);
            $table->text("descricao")->nullable();
            $table->timestamps();

            $table->foreign("id_usuario")->references("id_usuario")->on("usuario")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("disciplina");
    }
};