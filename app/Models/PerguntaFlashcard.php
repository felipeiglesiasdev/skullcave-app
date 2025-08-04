<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerguntaFlashcard extends Model
{
    // CONFIGURAÇÕES BÁSICAS
    protected $table = 'perguntas_flashcards';
    protected $primaryKey = 'id_pergunta';
    
    // CAMPOS PREENCHÍVEIS
    protected $fillable = [
        'id_flashcard',
        'pergunta',
        'resposta'
    ];

    // RELACIONAMENTOS
    public function flashcard()
    {
        return $this->belongsTo(FlashcardIndependente::class, 'id_flashcard', 'id_flashcard');
    }
}
