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
        Schema::create("turma", function (Blueprint $table) {
            $table->increments("id_turma");
            $table->unsignedInteger("id_escola");
            $table->unsignedInteger("id_professor");
            $table->string("nome_turma", 255);
            $table->timestamps();

            $table->foreign("id_escola")->references("id_escola")->on("escola")->onDelete("cascade");
            $table->foreign("id_professor")->references("id_usuario")->on("professor")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("turma");
    }
};