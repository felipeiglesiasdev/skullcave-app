<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicoIndependente extends Model
{
    // CONFIGURAÇÕES BÁSICAS
    protected $table = 'topicos_independentes';
    protected $primaryKey = 'id_topico';
    
    // CAMPOS PREENCHÍVEIS
    protected $fillable = [
        'id_disciplina',
        'nome',
        'descricao'
    ];

    // RELACIONAMENTOS
    public function disciplina()
    {
        return $this->belongsTo(DisciplinaIndependente::class, 'id_disciplina', 'id_disciplina');
    }

    public function flashcards()
    {
        return $this->hasMany(FlashcardIndependente::class, 'id_topico', 'id_topico');
    }

    // MÉTODOS AUXILIARES BÁSICOS
    public function getTotalFlashcardsAttribute()
    {
        return $this->flashcards()->count();
    }
}