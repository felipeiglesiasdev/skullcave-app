<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ====================== MODEL USUARIO ======================
 * ESSA CLASSE REPRESENTA A TABELA 'usuarios' NO BANCO DE DADOS.
 * HERDA DE 'Authenticatable' PARA USAR O SISTEMA DE AUTENTICAÇÃO DO LARAVEL.
 */
class Usuario extends Authenticatable
{
    use Notifiable; // PERMITE ENVIAR NOTIFICAÇÕES (EMAIL, SMS, ETC)

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    protected $table = 'usuarios'; // DEFINE O NOME DA TABELA NO BANCO
    protected $primaryKey = 'id_usuario'; // DEFINE A CHAVE PRIMÁRIA
    public $timestamps = false; // DESABILITA OS CAMPOS 'created_at' E 'updated_at'

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    protected $fillable = [
        'nome', 
        'email',
        'senha',
        'tipo',
        'data_cadastro'
    ];

    // ====================== CAMPOS OCULTOS ======================
    protected $hidden = [
        'senha', // OCULTA A SENHA NAS RESPOSTAS JSON
        'remember_token' // TOKEN DE LEMBRETE DE LOGIN
    ];

    // ====================== CONVERSÕES DE TIPOS ======================
    protected $casts = [
        'data_cadastro' => 'datetime', // CONVERTE 'data_cadastro' PARA TIPO DATETIME
    ];

    // ====================== RELACIONAMENTOS ======================
    
    // RELACIONA USUÁRIO COM SEU PERFIL DE PROFESSOR (1:1)
    public function perfilProfessor()
    {
        return $this->hasOne(Professor::class, 'id_usuario');
    }

    // RELACIONA USUÁRIO COM SEU PERFIL DE ALUNO (1:1)
    public function perfilAluno()
    {
        return $this->hasOne(Aluno::class, 'id_usuario');
    }

    // RELACIONA USUÁRIO COM SEU PERFIL INDEPENDENTE (1:1)
    public function perfilIndependente()
    {
        return $this->hasOne(Independente::class, 'id_usuario');
    }

    // ====================== MÉTODO DE AUTENTICAÇÃO ======================
    // SOBRESCREVE O MÉTODO PADRÃO DO LARAVEL PARA USAR 'senha' EM VEZ DE 'password'
    public function getAuthPassword()
    {
        return $this->senha;
    }
}