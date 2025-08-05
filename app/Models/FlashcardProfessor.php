<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL FLASHCARD PROFESSOR ======================
// ESSA CLASSE REPRESENTA A TABELA QUE VINCULA FLASHCARDS A TÓPICOS DE PROFESSORES.
// UM FLASHCARD CRIADO POR UM PROFESSOR PARA UM TÓPICO ESPECÍFICO.
class FlashcardProfessor extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'flashcard_professor';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_flashcard_professor';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_topico_professor', // CHAVE ESTRANGEIRA PARA 'TOPICO_PROFESSOR'
        'titulo',
        'descricao',
        'data_criacao'
    ];

    // ====================== CONVERSÕES DE TIPOS ======================
    // DEFINE AS CONVERSÕES DE TIPOS PARA OS ATRIBUTOS
    protected $casts = [
        'data_criacao' => 'datetime' // CONVERTE 'DATA_CRIACAO' PARA TIPO DATETIME
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA FLASHCARD PROFESSOR COM O TÓPICO DO PROFESSOR A QUAL ELE PERTENCE (PERTENCE A UM TOPICO_PROFESSOR)
    public function topicoProfessor()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO TOPICOPROFESSOR, USANDO 'ID_TOPICO_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(TopicoProfessor::class, 'id_topico_professor');
    }

    // RELACIONA FLASHCARD PROFESSOR COM SUAS PERGUNTAS (UM PARA MUITOS)
    public function perguntas()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO PERGUNTAFLASHCARD, USANDO 'ID_FLASHCARD_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(PerguntaFlashcard::class, 'id_flashcard_professor', 'id_flashcard_professor');
    }

    // ====================== MÉTODOS AUXILIARES ======================

    // RETORNA O TOTAL DE PERGUNTAS ASSOCIADAS A ESTE FLASHCARD DO PROFESSOR
    public function getTotalPerguntasAttribute()
    {
        // CONTA O NÚMERO DE PERGUNTAS RELACIONADAS
        return $this->perguntas()->count();
    }
}