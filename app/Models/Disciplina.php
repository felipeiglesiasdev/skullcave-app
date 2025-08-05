<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

// ====================== MODEL DISCIPLINA ======================
// ESSA CLASSE REPRESENTA A TABELA 'DISCIPLINA' NO BANCO DE DADOS.
// UMA DISCIPLINA PODE SER CRIADA POR UM USUÁRIO (ALUNO OU INDEPENDENTE).
class Disciplina extends Model
{
    // USA O TRAIT HASFACTORY PARA HABILITAR AS FACTORIES DE MODELO
    use HasFactory;

    // ====================== CONFIGURAÇÕES DA TABELA ======================
    // DEFINE O NOME DA TABELA NO BANCO
    protected $table = 'disciplina';
    // DEFINE A CHAVE PRIMÁRIA DA TABELA
    protected $primaryKey = 'id_disciplina';
    // INDICA QUE A TABELA POSSUI OS CAMPOS 'CREATED_AT' E 'UPDATED_AT'
    public $timestamps = true;

    // ====================== CAMPOS PREENCHÍVEIS (MASS ASSIGNMENT) ======================
    // DEFINE OS ATRIBUTOS QUE PODEM SER ATRIBUÍDOS EM MASSA
    protected $fillable = [
        'id_usuario', // CHAVE ESTRANGEIRA PARA 'USUARIO' (CRIADOR DA DISCIPLINA)
        'nome',       // NOME DA DISCIPLINA
        'descricao'   // DESCRIÇÃO DA DISCIPLINA
    ];

    // ====================== RELACIONAMENTOS ======================

    // RELACIONA DISCIPLINA COM O USUÁRIO QUE A CRIOU (PERTENCE A UM USUÁRIO)
    public function usuario()
    {
        // RETORNA O RELACIONAMENTO BELONGSTO COM O MODELO USUARIO, USANDO 'ID_USUARIO' COMO CHAVE ESTRANGEIRA
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // RELACIONA DISCIPLINA COM SEUS TÓPICOS (UM PARA MUITOS)
    public function topicos()
    {
        // RETORNA O RELACIONAMENTO HASMANY COM O MODELO TOPICO, USANDO 'ID_DISCIPLINA' COMO CHAVE ESTRANGEIRA
        return $this->hasMany(Topico::class, 'id_disciplina', 'id_disciplina');
    }

    // ====================== MÉTODOS AUXILIARES ======================

    // RETORNA O TOTAL DE TÓPICOS ASSOCIADOS A ESTA DISCIPLINA
    public function getTotalTopicosAttribute()
    {
        // CONTA O NÚMERO DE TÓPICOS RELACIONADOS
        return $this->topicos()->count();
    }

    // RETORNA O TOTAL DE FLASHCARDS ASSOCIADOS A ESTA DISCIPLINA (ATRAVÉS DOS TÓPICOS)
    public function getTotalFlashcardsAttribute()
    {
        // CARREGA OS TÓPICOS COM A CONTAGEM DE FLASHCARDS E SOMA OS TOTAIS
        return $this->topicos()->withCount('flashcards')->get()->sum('flashcards_count');
    }
}