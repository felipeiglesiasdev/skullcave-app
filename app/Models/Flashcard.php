<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL FLASHCARD ======================
// ESSA CLASSE REPRESENTA A TABELA 'FLASHCARD' NO BANCO DE DADOS.
// UM FLASHCARD PERTENCE A UM TÓPICO E PODE CONTER VÁRIAS PERGUNTAS.
class Flashcard extends Model
{
    use HasFactory;
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'flashcard';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_flashcard';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;
    
    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_topico',    // CHAVE ESTRANGEIRA PARA 'TOPICO'
        'titulo',       // TÍTULO DO FLASHCARD
        'descricao',    // DESCRIÇÃO DO FLASHCARD
        'data_criacao'  // DATA DE CRIAÇÃO DO FLASHCARD
    ];

    // ====================== CONVERSÕES DE TIPOS ======================
    // DEFINE AS CONVERSÕES DE TIPOS PARA OS ATRIBUTOS
    protected $casts = [
        'data_criacao' => 'datetime' // CONVERTE 'DATA_CRIACAO' PARA TIPO DATETIME
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA FLASHCARD COM O TÓPICO A QUAL ELE PERTENCE (PERTENCE A UM TÓPICO)
    public function topico()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO TOPICO, USANDO 'ID_TOPICO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Topico::class, 'id_topico', 'id_topico');
    }

    // RELACIONA FLASHCARD COM SUAS PERGUNTAS (UM PARA MUITOS)
    public function perguntas()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO PERGUNTAFLASHCARD, USANDO 'ID_FLASHCARD' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(PerguntaFlashcard::class, 'id_flashcard', 'id_flashcard');
    }

    // ====================== MÉTODOS AUXILIARES ======================

    // RETORNA O TOTAL DE PERGUNTAS ASSOCIADAS A ESTE FLASHCARD
    public function getTotalPerguntasAttribute()
    {
        // CONTA O NÚMERO DE PERGUNTAS RELACIONADAS
        return $this->perguntas()->count();
    }
}