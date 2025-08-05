<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL PERGUNTA FLASHCARD ======================
// ESSA CLASSE REPRESENTA A TABELA 'PERGUNTA_FLASHCARD' NO BANCO DE DADOS.
// UMA PERGUNTA DE FLASHCARD PERTENCE A UM FLASHCARD ESPECÍFICO.
class PerguntaFlashcard extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'pergunta_flashcard';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_pergunta';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;
    
    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_flashcard', // CHAVE ESTRANGEIRA PARA 'FLASHCARD'
        'pergunta',     // TEXTO DA PERGUNTA
        'resposta'      // TEXTO DA RESPOSTA
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA PERGUNTA FLASHCARD COM O FLASHCARD A QUAL ELA PERTENCE (PERTENCE A UM FLASHCARD)
    public function flashcard()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO FLASHCARD, USANDO 'ID_FLASHCARD' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Flashcard::class, 'id_flashcard', 'id_flashcard');
    }
}
