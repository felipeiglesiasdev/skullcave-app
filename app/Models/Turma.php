<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL TURMA ======================
// ESSA CLASSE REPRESENTA A TABELA 'TURMA' NO BANCO DE DADOS.
// UMA TURMA PERTENCE A UMA ESCOLA E É MINISTRADA POR UM PROFESSOR.
class Turma extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'turma';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_turma';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_escola',    // CHAVE ESTRANGEIRA PARA 'ESCOLA'
        'id_professor', // CHAVE ESTRANGEIRA PARA 'PROFESSOR'
        'nome_turma'    // NOME DA TURMA
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA TURMA COM A ESCOLA A QUAL ELA PERTENCE (PERTENCE A UMA ESCOLA)
    public function escola()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO ESCOLA, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Escola::class, 'id_escola');
    }

    // RELACIONA TURMA COM O PROFESSOR QUE A MINISTRA (PERTENCE A UM PROFESSOR)
    public function professor()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO PROFESSOR, USANDO 'ID_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Professor::class, 'id_professor', 'id_usuario');
    }

    // RELACIONA TURMA COM OS ALUNOS MATRICULADOS NELA (MUITOS PARA MUITOS)
    public function alunos()
    {
        // RETORNA O RELACIONAMENTO BELONGSTOMANY COM O MODELO ALUNO, ATRAVÉS DA TABELA PIVÔ 'TURMA_ALUNO'
        return $this->belongsToMany(Aluno::class, 'turma_aluno', 'id_turma', 'id_usuario_aluno');
    }

    // RELACIONA TURMA COM AS DISCIPLINAS MINISTRADAS PELO PROFESSOR NESSA TURMA (UM PARA MUITOS)
    public function disciplinasProfessor()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO DISCIPLINAPROFESSOR, USANDO 'ID_TURMA' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(DisciplinaProfessor::class, 'id_turma');
    }
}