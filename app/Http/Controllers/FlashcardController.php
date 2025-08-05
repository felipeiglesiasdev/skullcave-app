<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Topico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashcardController extends Controller
{
    // Lista todos os flashcardds de um tópico específico
    public function index($topicoId)
    {
        // Verifica se o tópico pertence ao usuário
        $topico = Topico::where("id_topico", $topicoId)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        $flashcards = $topico->flashcards()->with('perguntas')->get();
        return response()->json($flashcards);
    }

    // Cria um novo flashcardd
    public function store(Request $request)
    {
        // Validação básica
        $request->validate([
            "titulo" => "required|string|max:255",
            "descricao" => "nullable|string",
            "topico_id" => "required|exists:topico,id_topico"
        ]);

        // Verifica se o tópico pertence ao usuário
        $topico = Topico::where("id_topico", $request->topico_id)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        // Cria o flashcardd
        $flashcard = Flashcard::create([
            "id_topico" => $request->topico_id,
            "titulo" => $request->titulo,
            "descricao" => $request->descricao,
            "data_criacao" => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard criado com sucesso!',
            'data' => $flashcard
        ], 201);
    }

    // Exibe um flashcardd específico
    public function show($id)
    {
        $flashcard = Flashcard::where("id_flashcard", $id)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->with('perguntas')
                                            ->firstOrFail();
        
        return response()->json($flashcard);
    }

    // Atualiza um flashcardd existente
    public function update(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            "titulo" => "required|string|max:255",
            "descricao" => "nullable|string",
        ]);

        // Busca o flashcardd do usuário
        $flashcard = Flashcard::where("id_flashcard", $id)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->firstOrFail();

        // Atualiza os dados
        $flashcard->update([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard atualizado com sucesso!',
            'data' => $flashcard
        ]);
    }

    // Remove um flashcardd
    public function destroy($id)
    {
        $flashcard = Flashcard::where("id_flashcard", $id)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->firstOrFail();
        
        $flashcard->delete();

        return response()->json([
            'success' => true,
            'message' => 'Flashcard excluído com sucesso!'
        ]);
    }
}