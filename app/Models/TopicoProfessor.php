<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL TÓPICO PROFESSOR ======================
// ESSA CLASSE REPRESENTA A TABELA QUE VINCULA TÓPICOS A DISCIPLINAS DE PROFESSORES.
// UM TÓPICO CRIADO POR UM PROFESSOR PARA UMA DISCIPLINA ESPECÍFICA.
class TopicoProfessor extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'topico_professor';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_topico_professor';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_disciplina_professor', // CHAVE ESTRANGEIRA PARA 'DISCIPLINA_PROFESSOR'
        'nome',
        'descricao'
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA TÓPICO PROFESSOR COM A DISCIPLINA DO PROFESSOR A QUAL ELE PERTENCE (PERTENCE A UMA DISCIPLINA_PROFESSOR)
    public function disciplinaProfessor()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO DISCIPLINAPROFESSOR, USANDO 'ID_DISCIPLINA_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(DisciplinaProfessor::class, 'id_disciplina_professor');
    }

    // RELACIONA TÓPICO PROFESSOR COM SEUS FLASHCARDS (UM PARA MUITOS)
    public function flashcardsProfessor()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO FLASHCARDPROFESSOR, USANDO 'ID_TOPICO_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(FlashcardProfessor::class, 'id_topico_professor');
    }
}