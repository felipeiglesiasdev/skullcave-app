<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardIndependente extends Model
{
    // CONFIGURAÇÕES BÁSICAS
    protected $table = 'flashcards_independentes';
    protected $primaryKey = 'id_flashcard';
    
    // CAMPOS PREENCHÍVEIS
    protected $fillable = [
        'id_topico',
        'titulo',
        'descricao',
        'data_criacao'
    ];

    // CASTING DE TIPOS
    protected $casts = [
        'data_criacao' => 'datetime'
    ];

    // RELACIONAMENTOS
    public function topico()
    {
        return $this->belongsTo(TopicoIndependente::class, 'id_topico', 'id_topico');
    }

    public function perguntas()
    {
        return $this->hasMany(PerguntaFlashcard::class, 'id_flashcard', 'id_flashcard');
    }

    // MÉTODOS AUXILIARES BÁSICOS
    public function getTotalPerguntasAttribute()
    {
        return $this->perguntas()->count();
    }
}