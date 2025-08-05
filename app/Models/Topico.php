<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL TÓPICO ======================
// ESSA CLASSE REPRESENTA A TABELA 'TOPICO' NO BANCO DE DADOS.
// UM TÓPICO PERTENCE A UMA DISCIPLINA E PODE CONTER VÁRIOS FLASHCARDS.
class Topico extends Model
{
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'topico';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_topico';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;
    
    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_disciplina', // CHAVE ESTRANGEIRA PARA 'DISCIPLINA'
        'nome',          // NOME DO TÓPICO
        'descricao'      // DESCRIÇÃO DO TÓPICO
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA TÓPICO COM A DISCIPLINA A QUAL ELE PERTENCE (PERTENCE A UMA DISCIPLINA)
    public function disciplina()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO DISCIPLINA, USANDO 'ID_DISCIPLINA' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Disciplina::class, 'id_disciplina', 'id_disciplina');
    }

    // RELACIONA TÓPICO COM SEUS FLASHCARDS (UM PARA MUITOS)
    public function flashcards()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO FLASHCARD, USANDO 'ID_TOPICO' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Flashcard::class, 'id_topico', 'id_topico');
    }

    // ====================== MÉTODOS AUXILIARES ======================

    // RETORNA O TOTAL DE FLASHCARDS ASSOCIADOS A ESTE TÓPICO
    public function getTotalFlashcardsAttribute()
    {
        // CONTA O NÚMERO DE FLASHCARDS RELACIONADOS
        return $this->flashcards()->count();
    }
}