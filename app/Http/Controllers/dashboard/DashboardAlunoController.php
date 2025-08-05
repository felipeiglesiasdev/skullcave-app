<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;
use App\Models\DisciplinaProfessor;
use App\Models\Topico;
use App\Models\TopicoProfessor;
use App\Models\Flashcard;
use App\Models\FlashcardProfessor;
use App\Models\Aluno;
use App\Models\TurmaAluno;
use App\Models\Escola;
use App\Models\Professor;

class DashboardAlunoController extends Controller
{
    /**
     * Exibe o dashboard específico para alunos
     */
    public function index()
    {
        $user = Auth::user();
        
        // Buscar informações do aluno
        $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
        
        if (!$aluno) {
            return redirect()->route('dashboard')->with('error', 'Perfil de aluno não encontrado');
        }

        // Buscar turma do aluno
        $turmaAluno = TurmaAluno::where('id_aluno', $aluno->id_aluno)->first();
        $escola = null;
        $professor = null;
        $disciplinasProfessor = collect();

        if ($turmaAluno) {
            $turma = $turmaAluno->turma;
            $escola = $turma->escola ?? null;
            $professor = $turma->professor ?? null;
            
            // Buscar disciplinas do professor para esta turma
            $disciplinasProfessor = DisciplinaProfessor::where('id_turma', $turma->id_turma)
                ->with(['topicos.flashcards.perguntas'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Buscar disciplinas próprias do aluno
        $minhasDisciplinas = Disciplina::where('id_usuario', $user->id_usuario)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estatísticas
        $totalMinhasTopicos = $minhasDisciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->count();
        });

        $totalMeusFlashcards = $minhasDisciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->count();
            });
        });

        $totalFlashcardsProfessor = $disciplinasProfessor->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->count();
            });
        });

        return view('dashboard.aluno', compact(
            'minhasDisciplinas',
            'disciplinasProfessor',
            'escola',
            'professor',
            'totalMinhasTopicos',
            'totalMeusFlashcards',
            'totalFlashcardsProfessor'
        ));
    }

    /**
     * API: Buscar disciplinas próprias do aluno
     */
    public function getMinhasDisciplinas()
    {
        $user = Auth::user();
        
        $disciplinas = Disciplina::where('id_usuario', $user->id_usuario)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'disciplinas' => $disciplinas,
            'tipo' => 'minha'
        ]);
    }

    /**
     * API: Buscar disciplinas do professor
     */
    public function getDisciplinasProfessor()
    {
        $user = Auth::user();
        
        // Buscar aluno e sua turma
        $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
        
        if (!$aluno) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de aluno não encontrado'
            ], 404);
        }

        $turmaAluno = TurmaAluno::where('id_aluno', $aluno->id_aluno)->first();
        
        if (!$turmaAluno) {
            return response()->json([
                'success' => true,
                'disciplinas' => [],
                'tipo' => 'professor',
                'message' => 'Aluno não está em nenhuma turma'
            ]);
        }

        $disciplinas = DisciplinaProfessor::where('id_turma', $turmaAluno->id_turma)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'disciplinas' => $disciplinas,
            'tipo' => 'professor'
        ]);
    }

    /**
     * API: Buscar tópicos de uma disciplina (própria ou do professor)
     */
    public function getTopicos($disciplinaId, $tipo)
    {
        $user = Auth::user();
        
        if ($tipo === 'minha') {
            // Verificar se a disciplina pertence ao usuário
            $disciplina = Disciplina::where('id_disciplina', $disciplinaId)
                ->where('id_usuario', $user->id_usuario)
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplina não encontrada ou sem permissão'
                ], 404);
            }

            $topicos = Topico::where('id_disciplina', $disciplinaId)
                ->with(['flashcards.perguntas'])
                ->orderBy('created_at', 'desc')
                ->get();

        } else if ($tipo === 'professor') {
            // Verificar se é disciplina do professor da turma do aluno
            $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
            $turmaAluno = TurmaAluno::where('id_aluno', $aluno->id_aluno)->first();
            
            $disciplina = DisciplinaProfessor::where('id_disciplina_professor', $disciplinaId)
                ->where('id_turma', $turmaAluno->id_turma)
                ->first();

            if (!$disciplina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disciplina do professor não encontrada'
                ], 404);
            }

            $topicos = TopicoProfessor::where('id_disciplina_professor', $disciplinaId)
                ->with(['flashcards.perguntas'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'topicos' => $topicos,
            'disciplina' => $disciplina,
            'tipo' => $tipo
        ]);
    }

    /**
     * API: Buscar flashcards de um tópico
     */
    public function getFlashcards($topicoId, $tipo)
    {
        $user = Auth::user();
        
        if ($tipo === 'minha') {
            // Verificar se o tópico pertence a uma disciplina do usuário
            $topico = Topico::whereHas('disciplina', function ($query) use ($user) {
                $query->where('id_usuario', $user->id_usuario);
            })->where('id_topico', $topicoId)->first();

            if (!$topico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tópico não encontrado ou sem permissão'
                ], 404);
            }

            $flashcards = Flashcard::where('id_topico', $topicoId)
                ->with(['perguntas'])
                ->orderBy('created_at', 'desc')
                ->get();

        } else if ($tipo === 'professor') {
            // Verificar se é tópico do professor
            $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
            $turmaAluno = TurmaAluno::where('id_aluno', $aluno->id_aluno)->first();
            
            $topico = TopicoProfessor::whereHas('disciplinaProfessor', function ($query) use ($turmaAluno) {
                $query->where('id_turma', $turmaAluno->id_turma);
            })->where('id_topico_professor', $topicoId)->first();

            if (!$topico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tópico do professor não encontrado'
                ], 404);
            }

            $flashcards = FlashcardProfessor::where('id_topico_professor', $topicoId)
                ->with(['perguntas'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'flashcards' => $flashcards,
            'topico' => $topico,
            'tipo' => $tipo
        ]);
    }

    /**
     * API: Buscar estatísticas do aluno
     */
    public function getEstatisticas()
    {
        $user = Auth::user();
        $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
        
        // Estatísticas das disciplinas próprias
        $minhasDisciplinas = Disciplina::where('id_usuario', $user->id_usuario)->get();
        $totalMinhasDisciplinas = $minhasDisciplinas->count();
        
        $totalMeusTopicos = Topico::whereIn('id_disciplina', $minhasDisciplinas->pluck('id_disciplina'))->count();
        
        $totalMeusFlashcards = Flashcard::whereHas('topico', function ($query) use ($minhasDisciplinas) {
            $query->whereIn('id_disciplina', $minhasDisciplinas->pluck('id_disciplina'));
        })->count();

        // Estatísticas das disciplinas do professor
        $turmaAluno = TurmaAluno::where('id_aluno', $aluno->id_aluno)->first();
        $totalDisciplinasProfessor = 0;
        $totalFlashcardsProfessor = 0;

        if ($turmaAluno) {
            $disciplinasProfessor = DisciplinaProfessor::where('id_turma', $turmaAluno->id_turma)->get();
            $totalDisciplinasProfessor = $disciplinasProfessor->count();
            
            $totalFlashcardsProfessor = FlashcardProfessor::whereHas('topicoProfessor', function ($query) use ($disciplinasProfessor) {
                $query->whereIn('id_disciplina_professor', $disciplinasProfessor->pluck('id_disciplina_professor'));
            })->count();
        }

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'minhas_disciplinas' => $totalMinhasDisciplinas,
                'meus_topicos' => $totalMeusTopicos,
                'meus_flashcards' => $totalMeusFlashcards,
                'disciplinas_professor' => $totalDisciplinasProfessor,
                'flashcards_professor' => $totalFlashcardsProfessor,
                'total_flashcards' => $totalMeusFlashcards + $totalFlashcardsProfessor
            ]
        ]);
    }

    /**
     * API: Criar nova disciplina (própria do aluno)
     */
    public function criarDisciplina(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();

        $disciplina = Disciplina::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_usuario' => $user->id_usuario
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Disciplina criada com sucesso!',
            'disciplina' => $disciplina
        ]);
    }

    /**
     * API: Criar novo tópico (apenas em disciplinas próprias)
     */
    public function criarTopico(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'disciplina_id' => 'required|exists:disciplinas,id_disciplina'
        ]);

        $user = Auth::user();

        // Verificar se a disciplina pertence ao usuário
        $disciplina = Disciplina::where('id_disciplina', $request->disciplina_id)
            ->where('id_usuario', $user->id_usuario)
            ->first();

        if (!$disciplina) {
            return response()->json([
                'success' => false,
                'message' => 'Você só pode criar tópicos em suas próprias disciplinas'
            ], 403);
        }

        $topico = Topico::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_disciplina' => $request->disciplina_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tópico criado com sucesso!',
            'topico' => $topico
        ]);
    }

    /**
     * API: Criar novo flashcard (apenas em tópicos próprios)
     */
    public function criarFlashcard(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'topico_id' => 'required|exists:topicos,id_topico'
        ]);

        $user = Auth::user();

        // Verificar se o tópico pertence a uma disciplina do usuário
        $topico = Topico::whereHas('disciplina', function ($query) use ($user) {
            $query->where('id_usuario', $user->id_usuario);
        })->where('id_topico', $request->topico_id)->first();

        if (!$topico) {
            return response()->json([
                'success' => false,
                'message' => 'Você só pode criar flashcards em seus próprios tópicos'
            ], 403);
        }

        $flashcard = Flashcard::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'id_topico' => $request->topico_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard criado com sucesso!',
            'flashcard' => $flashcard
        ]);
    }
}

