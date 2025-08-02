<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ====================== MODEL INDEPENDENTE ======================
 * REPRESENTA A TABELA 'independentes' NO BANCO DE DADOS.
 * UM USUÁRIO INDEPENDENTE NÃO TEM VÍNCULO COM ESCOLAS.
 */
class Independente extends Model
{
    // ====================== CONFIGURAÇÕES DA TABELA ======================
    protected $table = 'independentes'; // NOME DA TABELA
    protected $primaryKey = 'id_usuario'; // CHAVE PRIMÁRIA
    public $timestamps = false; // SEM TIMESTAMPS AUTOMÁTICOS

    // ====================== CAMPOS PREENCHÍVEIS ======================
    protected $fillable = ['id_usuario']; // CHAVE ESTRANGEIRA PARA 'usuarios'

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA INDEPENDENTE COM SEU USUÁRIO (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}