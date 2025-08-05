<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL PROFESSOR ======================
// ESSA CLASSE REPRESENTA A TABELA 'PROFESSOR' NO BANCO DE DADOS.
// UM PROFESSOR É UM TIPO DE USUÁRIO COM INFORMAÇÕES ADICIONAIS.
class Professor extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'professor';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA, QUE TAMBÉM É UMA CHAVE ESTRANGEIRA PARA USUARIO
    protected $primaryKey = 'id_usuario';
    // INDICA QUE A TABELA NÃO POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = false;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_usuario', // CHAVE ESTRANGEIRA PARA 'USUARIO'
        'id_escola',  // CHAVE ESTRANGEIRA PARA 'ESCOLA'
        'titulacao'   // CAMPO DE TITULAÇÃO (EX: MESTRE, DOUTOR)
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA PROFESSOR COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO USUARIO, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // RELACIONA PROFESSOR COM UMA ESCOLA (PERTENCE A UMA ESCOLA)
    public function escola()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO ESCOLA, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Escola::class, 'id_escola');
    }

    // RELACIONA PROFESSOR COM AS TURMAS QUE ELE MINISTRA (UM PARA MUITOS)
    public function turmas()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO TURMA, USANDO 'ID_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Turma::class, 'id_professor');
    }

    // RELACIONA PROFESSOR COM AS DISCIPLINAS QUE ELE MINISTRA (UM PARA MUITOS)
    public function disciplinasProfessor()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO DISCIPLINAPROFESSOR, USANDO 'ID_PROFESSOR' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(DisciplinaProfessor::class, 'id_professor');
    }
}