<?php

namespace App\Http\Controllers;

use App\Models\PerguntaFlashcard;
use App\Models\FlashcardIndependente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerguntaFlashcardController extends Controller
{
    // Lista todas as perguntas de um flashcard específico
    public function index($flashcardId)
    {
        // Verifica se o flashcard pertence ao usuário
        $flashcard = FlashcardIndependente::where("id_flashcard", $flashcardId)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->firstOrFail();

        $perguntas = $flashcard->perguntas;
        return response()->json($perguntas);
    }

    // Cria uma nova pergunta
    public function store(Request $request)
    {
        // Validação básica
        $request->validate([
            "pergunta" => "required|string",
            "resposta" => "required|string",
            "flashcard_id" => "required|exists:flashcards_independentes,id_flashcard"
        ]);

        // Verifica se o flashcard pertence ao usuário
        $flashcard = FlashcardIndependente::where("id_flashcard", $request->flashcard_id)
                                            ->whereHas("topico.disciplina", function ($query) {
                                                $query->where("id_usuario", Auth::id());
                                            })
                                            ->firstOrFail();

        // Cria a pergunta
        $pergunta = PerguntaFlashcard::create([
            "id_flashcard" => $request->flashcard_id,
            "pergunta" => $request->pergunta,
            "resposta" => $request->resposta,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pergunta criada com sucesso!',
            'data' => $pergunta
        ], 201);
    }

    // Exibe uma pergunta específica
    public function show($id)
    {
        $pergunta = PerguntaFlashcard::where("id_pergunta", $id)
                                    ->whereHas("flashcard.topico.disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();
        
        return response()->json($pergunta);
    }

    // Atualiza uma pergunta existente
    public function update(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            "pergunta" => "required|string",
            "resposta" => "required|string",
        ]);

        // Busca a pergunta do usuário
        $pergunta = PerguntaFlashcard::where("id_pergunta", $id)
                                    ->whereHas("flashcard.topico.disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        // Atualiza os dados
        $pergunta->update([
            'pergunta' => $request->pergunta,
            'resposta' => $request->resposta,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pergunta atualizada com sucesso!',
            'data' => $pergunta
        ]);
    }

    // Remove uma pergunta
    public function destroy($id)
    {
        $pergunta = PerguntaFlashcard::where("id_pergunta", $id)
                                    ->whereHas("flashcard.topico.disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();
        
        $pergunta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pergunta excluída com sucesso!'
        ]);
    }
}