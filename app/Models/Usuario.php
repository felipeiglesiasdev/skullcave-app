<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL USUARIO ======================
// ESSA CLASSE REPRESENTA A TABELA 'USUARIO' NO BANCO DE DADOS.
// HERDA DE 'AUTHENTICATABLE' PARA USAR O SISTEMA DE AUTENTICAÇÃO DO LARAVEL.
class Usuario extends Authenticatable
{
    use HasFactory;


    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO DE DADOS
    protected $table = 'usuario';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_usuario';
    // INDICA QUE A TABELA NÃO POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = false;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'nome', // NOME DO USUÁRIO
        'email', // ENDEREÇO DE E-MAIL DO USUÁRIO
        'senha', // SENHA DO USUÁRIO
        'tipo', // TIPO DE USUÁRIO (ADMIN, PROFESSOR, ALUNO, INDEPENDENTE)
        'data_cadastro' // DATA DE CADASTRO DO USUÁRIO
    ];

    // ====================== CAMPOS OCULTOS ======================
    // DEFINE OS ATRIBUTOS QUE DEVEM SER OCULTADOS NAS RESPOSTAS JSON
    protected $hidden = [
        'senha', // OCULTA A SENHA NAS RESPOSTAS JSON
        'remember_token' // OCULTA O TOKEN DE LEMBRETE DE LOGIN
    ];

    // ====================== CONVERSÕES DE TIPOS ======================
    // DEFINE AS CONVERSÕES DE TIPOS PARA OS ATRIBUTOS
    protected $casts = [
        'data_cadastro' => 'datetime', // CONVERTE 'DATA_CADASTRO' PARA TIPO DATETIME
    ];

    // ====================== RELACIONAMENTOS ======================
    
    // RELACIONA USUÁRIO COM SEU PERFIL DE PROFESSOR (UM PARA UM)
    public function perfilProfessor()
    {
        // RETORNA O RELACIONAMENTO HASONE COM O MODELO PROFESSOR, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->hasOne(Professor::class, 'id_usuario');
    }

    // RELACIONA USUÁRIO COM SEU PERFIL DE ALUNO (UM PARA UM)
    public function perfilAluno()
    {
        // RETORNA O RELACIONAMENTO HASONE COM O MODELO ALUNO, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->hasOne(Aluno::class, 'id_usuario');
    }

    // RELACIONA USUÁRIO COM SEU PERFIL INDEPENDENTE (UM PARA UM)
    public function perfilIndependente()
    {
        // RETORNA O RELACIONAMENTO HASONE COM O MODELO INDEPENDENTE, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->hasOne(Independente::class, 'id_usuario');
    }

    // RELACIONA USUÁRIO COM SUAS DISCIPLINAS INDEPENDENTES (UM PARA MUITOS)
    public function disciplinas()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO DISCIPLINA, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Disciplina::class, 'id_usuario', 'id_usuario');
    }
    
    // ====================== MÉTODO DE AUTENTICAÇÃO ======================
    // SOBRESCREVE O MÉTODO PADRÃO DO LARAVEL PARA USAR 'SENHA' EM VEZ DE 'PASSWORD'
    public function getAuthPassword()
    {
        // RETORNA O VALOR DO ATRIBUTO 'SENHA'
        return $this->senha;
    }
}