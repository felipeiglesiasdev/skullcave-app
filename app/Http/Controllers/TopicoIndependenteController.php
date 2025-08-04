<?php

namespace App\Http\Controllers;

use App\Models\TopicoIndependente;
use App\Models\DisciplinaIndependente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicoIndependenteController extends Controller
{
    // Lista todos os tópicos de uma disciplina específica
    public function index($disciplinaId)
    {
        // Verifica se a disciplina pertence ao usuário
        $disciplina = DisciplinaIndependente::where("id_disciplina", $disciplinaId)
                                            ->where("id_usuario", Auth::id())
                                            ->firstOrFail();

        $topicos = $disciplina->topicos()->with('flashcards')->get();
        return response()->json($topicos);
    }

    // Cria um novo tópico
    public function store(Request $request)
    {
        // Validação básica
        $request->validate([
            "nome" => "required|string|max:255",
            "descricao" => "nullable|string",
            "disciplina_id" => "required|exists:disciplinas_independentes,id_disciplina"
        ]);

        // Verifica se a disciplina pertence ao usuário
        $disciplina = DisciplinaIndependente::where("id_disciplina", $request->disciplina_id)
                                            ->where("id_usuario", Auth::id())
                                            ->firstOrFail();

        // Cria o tópico
        $topico = TopicoIndependente::create([
            "id_disciplina" => $request->disciplina_id,
            "nome" => $request->nome,
            "descricao" => $request->descricao,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tópico criado com sucesso!',
            'data' => $topico
        ], 201);
    }

    // Exibe um tópico específico
    public function show($id)
    {
        $topico = TopicoIndependente::where("id_topico", $id)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->with('flashcards.perguntas')
                                    ->firstOrFail();
        
        return response()->json($topico);
    }

    // Atualiza um tópico existente
    public function update(Request $request, $id)
    {
        // Validação básica
        $request->validate([
            "nome" => "required|string|max:255",
            "descricao" => "nullable|string",
        ]);

        // Busca o tópico do usuário
        $topico = TopicoIndependente::where("id_topico", $id)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();

        // Atualiza os dados
        $topico->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tópico atualizado com sucesso!',
            'data' => $topico
        ]);
    }

    // Remove um tópico
    public function destroy($id)
    {
        $topico = TopicoIndependente::where("id_topico", $id)
                                    ->whereHas("disciplina", function ($query) {
                                        $query->where("id_usuario", Auth::id());
                                    })
                                    ->firstOrFail();
        
        $topico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tópico excluído com sucesso!'
        ]);
    }
}