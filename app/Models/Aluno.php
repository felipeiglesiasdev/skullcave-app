<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ====================== MODEL ALUNO ======================
 * REPRESENTA A TABELA 'alunos' NO BANCO DE DADOS.
 * UM ALUNO É UM TIPO DE USUÁRIO VINCULADO A UMA ESCOLA.
 */
class Aluno extends Model
{
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    protected $table = 'alunos'; // NOME DA TABELA
    protected $primaryKey = 'id_usuario'; // CHAVE PRIMÁRIA
    public $timestamps = false; // SEM TIMESTAMPS AUTOMÁTICOS

    // ====================== CAMPOS PREENCHÍVEIS ======================
    protected $fillable = [
        'id_usuario', // CHAVE ESTRANGEIRA PARA 'usuarios'
        'id_escola',  // CHAVE ESTRANGEIRA PARA 'escolas'
        'matricula'   // NÚMERO DE MATRÍCULA DO ALUNO
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA ALUNO COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // RELACIONA ALUNO COM UMA ESCOLA (PERTENCE A UMA ESCOLA)
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'id_escola');
    }
}