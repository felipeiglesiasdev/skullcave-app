<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ====================== MODEL PROFESSOR ======================
 * REPRESENTA A TABELA 'professores' NO BANCO DE DADOS.
 * UM PROFESSOR É UM TIPO DE USUÁRIO COM INFORMAÇÕES ADICIONAIS.
 */
class Professor extends Model
{
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    protected $table = 'professores'; // NOME DA TABELA
    protected $primaryKey = 'id_usuario'; // CHAVE PRIMÁRIA
    public $timestamps = false; // SEM TIMESTAMPS AUTOMÁTICOS

    // ====================== CAMPOS PREENCHÍVEIS ======================
    protected $fillable = [
        'id_usuario', // CHAVE ESTRANGEIRA PARA 'usuarios'
        'id_escola',  // CHAVE ESTRANGEIRA PARA 'escolas'
        'titulacao'   // CAMPO DE TITULAÇÃO (EX: MESTRE, DOUTOR)
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA PROFESSOR COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // RELACIONA PROFESSOR COM UMA ESCOLA (PERTENCE A UMA ESCOLA)
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'id_escola');
    }
}