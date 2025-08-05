<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL INDEPENDENTE ======================
// ESSA CLASSE REPRESENTA A TABELA 'INDEPENDENTE' NO BANCO DE DADOS.
// UM USUÁRIO INDEPENDENTE NÃO TEM VÍNCULO COM ESCOLAS.
class Independente extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'independente';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA, QUE TAMBÉM É UMA CHAVE ESTRANGEIRA PARA USUARIO
    protected $primaryKey = 'id_usuario';
    // INDICA QUE A TABELA NÃO POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = false;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_usuario' // CHAVE ESTRANGEIRA PARA 'USUARIO'
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA INDEPENDENTE COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO USUARIO, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}