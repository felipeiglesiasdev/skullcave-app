<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL TURMA ALUNO ======================
// ESSA CLASSE REPRESENTA A TABELA PIVÔ PARA O RELACIONAMENTO MUITOS-PARA-MUITOS ENTRE TURMAS E ALUNOS.
class TurmaAluno extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'turma_aluno';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_turma_aluno';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_turma',         // CHAVE ESTRANGEIRA PARA 'TURMA'
        'id_usuario_aluno'  // CHAVE ESTRANGEIRA PARA 'ALUNO' (ID DO USUÁRIO ALUNO)
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA TURMA ALUNO COM A TURMA (PERTENCE A UMA TURMA)
    public function turma()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO TURMA, USANDO 'ID_TURMA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Turma::class, 'id_turma');
    }

    //REVER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // RELACIONA TURMA ALUNO COM O ALUNO (PERTENCE A UM ALUNO)
    public function aluno()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO ALUNO, USANDO 'ID_USUARIO_ALUNO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Aluno::class, 'id_usuario_aluno', 'id_usuario');
    }
    //REVER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}