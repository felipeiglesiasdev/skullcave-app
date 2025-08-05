<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disciplina;
use App\Models\Topico;
use App\Models\Flashcard;

class DashboardIndependenteController extends Controller
{
    /**
     * Exibe o dashboard específico para usuários independentes
     */
    public function index()
    {
        $user = Auth::user();
        
        // Buscar disciplinas do usuário independente
        $disciplinas = Disciplina::where('id_usuario', $user->id_usuario)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estatísticas
        $totalTopicos = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->count();
        });

        $totalFlashcards = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->count();
            });
        });

        $totalPerguntas = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->sum(function ($flashcard) {
                    return $flashcard->perguntas->count();
                });
            });
        });

        return view('dashboard.independente', compact(
            'disciplinas',
            'totalTopicos',
            'totalFlashcards',
            'totalPerguntas'
        ));
    }

    /**
     * API: Buscar disciplinas do usuário independente
     */
    public function getDisciplinas()
    {
        $user = Auth::user();
        
        $disciplinas = Disciplina::where('id_usuario', $user->id_usuario)
            ->with(['topicos.flashcards.perguntas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'disciplinas' => $disciplinas
        ]);
    }

    /**
     * API: Buscar tópicos de uma disciplina específica
     */
    public function getTopicos($disciplinaId)
    {
        $user = Auth::user();
        
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

        return response()->json([
            'success' => true,
            'topicos' => $topicos,
            'disciplina' => $disciplina
        ]);
    }

    /**
     * API: Buscar flashcards de um tópico específico
     */
    public function getFlashcards($topicoId)
    {
        $user = Auth::user();
        
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

        return response()->json([
            'success' => true,
            'flashcards' => $flashcards,
            'topico' => $topico
        ]);
    }

    /**
     * API: Buscar estatísticas do usuário independente
     */
    public function getEstatisticas()
    {
        $user = Auth::user();
        
        $disciplinas = Disciplina::where('id_usuario', $user->id_usuario)->get();
        
        $totalDisciplinas = $disciplinas->count();
        
        $totalTopicos = Topico::whereIn('id_disciplina', $disciplinas->pluck('id_disciplina'))->count();
        
        $totalFlashcards = Flashcard::whereHas('topico', function ($query) use ($disciplinas) {
            $query->whereIn('id_disciplina', $disciplinas->pluck('id_disciplina'));
        })->count();

        $totalPerguntas = \App\Models\PerguntaFlashcard::whereHas('flashcard.topico', function ($query) use ($disciplinas) {
            $query->whereIn('id_disciplina', $disciplinas->pluck('id_disciplina'));
        })->count();

        // Disciplinas mais recentes
        $disciplinasRecentes = Disciplina::where('id_usuario', $user->id_usuario)
            ->with(['topicos'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Atividade recente (flashcards criados nos últimos 7 dias)
        $flashcardsRecentes = Flashcard::whereHas('topico', function ($query) use ($disciplinas) {
            $query->whereIn('id_disciplina', $disciplinas->pluck('id_disciplina'));
        })->where('created_at', '>=', now()->subDays(7))->count();

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'total_disciplinas' => $totalDisciplinas,
                'total_topicos' => $totalTopicos,
                'total_flashcards' => $totalFlashcards,
                'total_perguntas' => $totalPerguntas,
                'disciplinas_recentes' => $disciplinasRecentes,
                'flashcards_recentes' => $flashcardsRecentes
            ]
        ]);
    }

    /**
     * API: Criar nova disciplina
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
     * API: Criar novo tópico
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
                'message' => 'Disciplina não encontrada ou sem permissão'
            ], 404);
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
     * API: Criar novo flashcard
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
                'message' => 'Tópico não encontrado ou sem permissão'
            ], 404);
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

