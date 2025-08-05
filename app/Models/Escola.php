<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL ESCOLA ======================
// ESSA CLASSE REPRESENTA A TABELA 'ESCOLA' NO BANCO DE DADOS.
// UMA ESCOLA PODE TER VÁRIOS PROFESSORES, ALUNOS E TURMAS.
class Escola extends Model
{
    use HasFactory;
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'escola';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_escola';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'nome',     // NOME DA ESCOLA
        'cnpj',     // CNPJ DA ESCOLA
        'endereco', // ENDEREÇO DA ESCOLA
        'telefone'  // TELEFONE DA ESCOLA
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA ESCOLA COM SEUS PROFESSORES (UM PARA MUITOS)
    public function professores()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO PROFESSOR, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Professor::class, 'id_escola');
    }

    // RELACIONA ESCOLA COM SEUS ALUNOS (UM PARA MUITOS)
    public function alunos()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO ALUNO, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Aluno::class, 'id_escola');
    }

    // RELACIONA ESCOLA COM SUAS TURMAS (UM PARA MUITOS)
    public function turmas()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO TURMA, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Turma::class, 'id_escola');
    }
}