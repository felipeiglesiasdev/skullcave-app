<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaIndependente extends Model
{
    // CONFIGURAÇÕES BÁSICAS
    protected $table = 'disciplinas_independentes'; // NOME DA TABELA
    protected $primaryKey = 'id_disciplina'; // CHAVE PRIMÁRIA
    // CAMPOS PREENCHÍVEIS
    protected $fillable = [
        'id_usuario',
        'nome',
        'descricao'
    ];

    // RELACIONAMENTOS
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario'); // UMA DISCIPLINA PERTENCE A UM USUÁRIO
    }

    public function topicos()
    {
        return $this->hasMany(TopicoIndependente::class, 'id_disciplina', 'id_disciplina'); // UMA DISCIPLINA POSSUI VARIOS TOPICOS 
    }

    // MÉTODOS AUXILIARES BÁSICOS

    // RETORNA O TOTAL DE TÓPICOS
    public function getTotalTopicosAttribute()
    {
        return $this->topicos()->count();
    }

    // RETORNA O TOTAL DE FLASHCARDS
    public function getTotalFlashcardsAttribute()
    {
        return $this->topicos()->withCount('flashcards')->get()->sum('flashcards_count');
    }
}
