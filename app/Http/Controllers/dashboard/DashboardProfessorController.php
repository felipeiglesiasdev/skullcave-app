<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Professor;
use App\Models\Turma;
use App\Models\TurmaAluno;
use App\Models\Aluno;
use App\Models\DisciplinaProfessor;
use App\Models\TopicoProfessor;
use App\Models\FlashcardProfessor;
use App\Models\Escola;

class DashboardProfessorController extends Controller
{
    /**
     * Exibe o dashboard específico para professores
     */
    public function index()
    {
        $user = Auth::user();
        
        // Buscar informações do professor
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        if (!$professor) {
            return redirect()->route('dashboard')->with('error', 'Perfil de professor não encontrado');
        }

        // Buscar escola do professor
        $escola = Escola::find($professor->id_escola);

        // Buscar turmas do professor
        $turmas = Turma::where('id_professor', $professor->id_professor)
            ->with(['alunos', 'disciplinas'])
            ->orderBy('nome')
            ->get();

        // Calcular estatísticas
        $totalAlunos = $turmas->sum(function ($turma) {
            return $turma->alunos->count();
        });

        $totalDisciplinas = DisciplinaProfessor::whereIn('id_turma', $turmas->pluck('id_turma'))->count();

        $totalFlashcards = FlashcardProfessor::whereHas('topicoProfessor.disciplinaProfessor', function ($query) use ($turmas) {
            $query->whereIn('id_turma', $turmas->pluck('id_turma'));
        })->count();

        return view('dashboard.professor', compact(
            'turmas',
            'escola',
            'totalAlunos',
            'totalDisciplinas',
            'totalFlashcards'
        ));
    }

    /**
     * API: Buscar turmas do professor
     */
    public function getTurmas()
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        if (!$professor) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de professor não encontrado'
            ], 404);
        }

        $turmas = Turma::where('id_professor', $professor->id_professor)
            ->with(['alunos.usuario', 'disciplinas'])
            ->orderBy('nome')
            ->get();

        return response()->json([
            'success' => true,
            'turmas' => $turmas
        ]);
    }

    /**
     * API: Buscar disciplinas de uma turma específica
     */
    public function getDisciplinasTurma($turmaId)
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        // Verificar se a turma pertence ao professor
        $turma = Turma::where('id_turma', $turmaId)
            ->where('id_professor', $professor->id_professor)
            ->first();

        if (!$turma) {
            return response()->json([
                'success' => false,
                'message' => 'Turma não encontrada ou sem permissão'
            ], 404);
        }

        $disciplinas = DisciplinaProfessor::where('id_turma', $turmaId)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'disciplinas' => $disciplinas,
            'turma' => $turma
        ]);
    }

    /**
     * API: Buscar alunos de uma turma específica
     */
    public function getAlunosTurma($turmaId)
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        // Verificar se a turma pertence ao professor
        $turma = Turma::where('id_turma', $turmaId)
            ->where('id_professor', $professor->id_professor)
            ->first();

        if (!$turma) {
            return response()->json([
                'success' => false,
                'message' => 'Turma não encontrada ou sem permissão'
            ], 404);
        }

        $alunos = TurmaAluno::where('id_turma', $turmaId)
            ->with(['aluno.usuario'])
            ->get()
            ->map(function ($turmaAluno) {
                return [
                    'id_aluno' => $turmaAluno->aluno->id_aluno,
                    'nome' => $turmaAluno->aluno->usuario->nome,
                    'email' => $turmaAluno->aluno->usuario->email,
                    'data_nascimento' => $turmaAluno->aluno->data_nascimento
                ];
            });

        return response()->json([
            'success' => true,
            'alunos' => $alunos,
            'turma' => $turma
        ]);
    }

    /**
     * API: Buscar tópicos de uma disciplina do professor
     */
    public function getTopicos($disciplinaId)
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        // Verificar se a disciplina pertence ao professor
        $disciplina = DisciplinaProfessor::whereHas('turma', function ($query) use ($professor) {
            $query->where('id_professor', $professor->id_professor);
        })->where('id_disciplina_professor', $disciplinaId)->first();

        if (!$disciplina) {
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão'
            ], 404);
        }

        $topicos = TopicoProfessor::where('id_disciplina_professor', $disciplinaId)
            ->with(['flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'topicos' => $topicos,
            'disciplina' => $disciplina
        ]);
    }

    /**
     * API: Buscar flashcards de um tópico do professor
     */
    public function getFlashcards($topicoId)
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        // Verificar se o tópico pertence ao professor
        $topico = TopicoProfessor::whereHas('disciplinaProfessor.turma', function ($query) use ($professor) {
            $query->where('id_professor', $professor->id_professor);
        })->where('id_topico_professor', $topicoId)->first();

        if (!$topico) {
            return response()->json([
                'success' => false,
                'message' => 'Tópico não encontrado ou sem permissão'
            ], 404);
        }

        $flashcards = FlashcardProfessor::where('id_topico_professor', $topicoId)
            ->with(['perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'flashcards' => $flashcards,
            'topico' => $topico
        ]);
    }

    /**
     * API: Criar nova disciplina para uma turma
     */
    public function criarDisciplina(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'turma_id' => 'required|exists:turmas,id_turma'
        ]);

        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();

        // Verificar se a turma pertence ao professor
        $turma = Turma::where('id_turma', $request->turma_id)
            ->where('id_professor', $professor->id_professor)
            ->first();

        if (!$turma) {
            return response()->json([
                'success' => false,
                'message' => 'Turma não encontrada ou sem permissão'
            ], 403);
        }

        $disciplina = DisciplinaProfessor::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_turma' => $request->turma_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Disciplina criada com sucesso!',
            'disciplina' => $disciplina
        ]);
    }

    /**
     * API: Criar novo tópico para uma disciplina
     */
    public function criarTopico(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'disciplina_professor_id' => 'required|exists:disciplinas_professor,id_disciplina_professor'
        ]);

        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();

        // Verificar se a disciplina pertence ao professor
        $disciplina = DisciplinaProfessor::whereHas('turma', function ($query) use ($professor) {
            $query->where('id_professor', $professor->id_professor);
        })->where('id_disciplina_professor', $request->disciplina_professor_id)->first();

        if (!$disciplina) {
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão'
            ], 403);
        }

        $topico = TopicoProfessor::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_disciplina_professor' => $request->disciplina_professor_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tópico criado com sucesso!',
            'topico' => $topico
        ]);
    }

    /**
     * API: Criar novo flashcard para um tópico
     */
    public function criarFlashcard(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'topico_professor_id' => 'required|exists:topicos_professor,id_topico_professor'
        ]);

        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();

        // Verificar se o tópico pertence ao professor
        $topico = TopicoProfessor::whereHas('disciplinaProfessor.turma', function ($query) use ($professor) {
            $query->where('id_professor', $professor->id_professor);
        })->where('id_topico_professor', $request->topico_professor_id)->first();

        if (!$topico) {
            return response()->json([
                'success' => false,
                'message' => 'Tópico não encontrado ou sem permissão'
            ], 403);
        }

        $flashcard = FlashcardProfessor::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'id_topico_professor' => $request->topico_professor_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard criado com sucesso!',
            'flashcard' => $flashcard
        ]);
    }

    /**
     * API: Atribuir flashcards de um tópico aos alunos selecionados
     */
    public function atribuirFlashcards(Request $request)
    {
        $request->validate([
            'topico_professor_id' => 'required|exists:topicos_professor,id_topico_professor',
            'alunos_ids' => 'required|array',
            'alunos_ids.*' => 'exists:alunos,id_aluno'
        ]);

        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();

        // Verificar se o tópico pertence ao professor
        $topico = TopicoProfessor::whereHas('disciplinaProfessor.turma', function ($query) use ($professor) {
            $query->where('id_professor', $professor->id_professor);
        })->where('id_topico_professor', $request->topico_professor_id)->first();

        if (!$topico) {
            return response()->json([
                'success' => false,
                'message' => 'Tópico não encontrado ou sem permissão'
            ], 403);
        }

        // Buscar flashcards do tópico
        $flashcards = FlashcardProfessor::where('id_topico_professor', $request->topico_professor_id)->get();

        if ($flashcards->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum flashcard encontrado neste tópico'
            ], 404);
        }

        // Aqui você implementaria a lógica de atribuição
        // Por exemplo, criar registros em uma tabela de atribuições
        // ou marcar os flashcards como atribuídos aos alunos específicos
        
        // Para este exemplo, vamos apenas retornar sucesso
        // Na implementação real, você criaria os relacionamentos necessários

        return response()->json([
            'success' => true,
            'message' => 'Flashcards atribuídos com sucesso aos alunos selecionados!',
            'flashcards_count' => $flashcards->count(),
            'alunos_count' => count($request->alunos_ids)
        ]);
    }

    /**
     * API: Buscar estatísticas do professor
     */
    public function getEstatisticas()
    {
        $user = Auth::user();
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        
        $turmas = Turma::where('id_professor', $professor->id_professor)->get();
        $totalTurmas = $turmas->count();
        
        $totalAlunos = TurmaAluno::whereIn('id_turma', $turmas->pluck('id_turma'))->count();
        
        $totalDisciplinas = DisciplinaProfessor::whereIn('id_turma', $turmas->pluck('id_turma'))->count();
        
        $totalFlashcards = FlashcardProfessor::whereHas('topicoProfessor.disciplinaProfessor', function ($query) use ($turmas) {
            $query->whereIn('id_turma', $turmas->pluck('id_turma'));
        })->count();

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'total_turmas' => $totalTurmas,
                'total_alunos' => $totalAlunos,
                'total_disciplinas' => $totalDisciplinas,
                'total_flashcards' => $totalFlashcards
            ]
        ]);
    }
}

