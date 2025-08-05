<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL ALUNO ======================
// ESSA CLASSE REPRESENTA A TABELA 'ALUNO' NO BANCO DE DADOS.
// UM ALUNO É UM TIPO DE USUÁRIO VINCULADO A UMA ESCOLA.
class Aluno extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'aluno';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA, QUE TAMBÉM É UMA CHAVE ESTRANGEIRA PARA USUARIO
    protected $primaryKey = 'id_usuario';
    // INDICA QUE A TABELA NÃO POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = false;

    // ====================== CAMPOS PREENCHÍVEIS  ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_usuario', // CHAVE ESTRANGEIRA PARA 'USUARIO'
        'id_escola',  // CHAVE ESTRANGEIRA PARA 'ESCOLA'
        'matricula'   // NÚMERO DE MATRÍCULA DO ALUNO
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA ALUNO COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO USUARIO, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // RELACIONA ALUNO COM UMA ESCOLA (PERTENCE A UMA ESCOLA)
    public function escola()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO ESCOLA, USANDO 'ID_ESCOLA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Escola::class, 'id_escola');
    }

    // RELACIONA ALUNO COM AS TURMAS QUE ELE ESTÁ MATRICULADO (MUITOS PARA MUITOS)
    public function turmas()
    {
        // RETORNA O RELACIONAMENTO BELONGSTOMANY COM O MODELO TURMA, ATRAVÉS DA TABELA PIVÔ 'TURMA_ALUNO'
        return $this->belongsToMany(Turma::class, 'turma_aluno', 'id_usuario_aluno', 'id_turma');
    }
}