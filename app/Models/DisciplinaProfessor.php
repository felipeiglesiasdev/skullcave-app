<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL DISCIPLINA PROFESSOR ======================
// ESSA CLASSE REPRESENTA A TABELA QUE VINCULA DISCIPLINAS A PROFESSORES E TURMAS.
// UMA DISCIPLINA MINISTRADA POR UM PROFESSOR EM UMA TURMA ESPECÍFICA.
class DisciplinaProfessor extends Model
{
    use HasFactory;
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'disciplina_professor';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_disciplina_professor';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_turma',     // CHAVE ESTRANGEIRA PARA 'TURMA'
        'id_professor', // CHAVE ESTRANGEIRA PARA 'PROFESSOR'
        'nome',         // NOME DA DISCIPLINA
        'descricao'     // DESCRIÇÃO DA DISCIPLINA
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA DISCIPLINA PROFESSOR COM A TURMA (PERTENCE A UMA TURMA)
    public function turma()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO TURMA, USANDO 'ID_TURMA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Turma::class, 'id_turma');
    }

    // RELACIONA DISCIPLINA PROFESSOR COM O PROFESSOR (PERTENCE A UM PROFESSOR)
    public function professor()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO PROFESSOR, USANDO 'ID_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Professor::class, 'id_professor', 'id_usuario');
    }

    // RELACIONA DISCIPLINA PROFESSOR COM SEUS TÓPICOS (UM PARA MUITOS)
    public function topicosProfessor()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO TOPICOPROFESSOR, USANDO 'ID_DISCIPLINA_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(TopicoProfessor::class, 'id_disciplina_professor');
    }
}