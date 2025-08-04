<?php

namespace App\Http\Controllers;

use App\Models\FlashcardIndependente;
use App\Models\TopicoIndependente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashcardIndependenteController extends Controller
{
    // Lista todos os flashcards de um tópico específico
    public function index($topicoId)
    {
        // Verifica se o tópico pertence ao usuário
        $topico = TopicoIndependente::where("id_topico", $topicoId)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        $flashcards = $topico->flashcards()->with('perguntas')->get();
        return response()->json($flashcards);
    }

    // Cria um novo flashcard
    public function store(Request $request)
    {
        // Validação básica
        $request->validate([
            "titulo" => "required|string|max:255",
            "descricao" => "nullable|string",
            "topico_id" => "required|exists:topicos_independentes,id_topico"
        ]);

        // Verifica se o tópico pertence ao usuário
        $topico = TopicoIndependente::where("id_topico", $request->topico_id)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        // Cria o flashcard
        $flashcard = FlashcardIndependente::create([
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

    // Exibe um flashcard específico
    public function show($id)
    {
        $flashcard = FlashcardIndependente::where("id_flashcard", $id)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->with('perguntas')
                                            ->firstOrFail();
        
        return response()->json($flashcard);
    }

    // Atualiza um flashcard existente
    public function update(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            "titulo" => "required|string|max:255",
            "descricao" => "nullable|string",
        ]);

        // Busca o flashcard do usuário
        $flashcard = FlashcardIndependente::where("id_flashcard", $id)
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

    // Remove um flashcard
    public function destroy($id)
    {
        $flashcard = FlashcardIndependente::where("id_flashcard", $id)
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