<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ====================== MODEL ESCOLA ======================
 * REPRESENTA A TABELA 'escolas' NO BANCO DE DADOS.
 * UMA ESCOLA PODE TER VÁRIOS PROFESSORES E ALUNOS.
 */
class Escola extends Model
{
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    protected $table = 'escolas'; // NOME DA TABELA
    protected $primaryKey = 'id_escola'; // CHAVE PRIMÁRIA
    public $timestamps = false; // SEM TIMESTAMPS AUTOMÁTICOS

    // ====================== CAMPOS PREENCHÍVEIS ======================
    protected $fillable = [
        'nome',     // NOME DA ESCOLA
        'cnpj',     // CNPJ DA ESCOLA
        'endereco', // ENDEREÇO DA ESCOLA
        'telefone'  // TELEFONE DA ESCOLA
    ];

    // ====================== RELACIONAMENTOS ======================

    // UMA ESCOLA TEM MUITOS PROFESSORES (HAS MANY)
    public function professores()
    {
        return $this->hasMany(Professor::class, 'id_escola');
    }

    // UMA ESCOLA TEM MUITOS ALUNOS (HAS MANY)
    public function alunos()
    {
        return $this->hasMany(Aluno::class, 'id_escola');
    }
}