<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplinaController extends Controller
{
    // LISTA TODAS AS DISCIPLINAS DO USUÁRIO AUTENTICADO
    public function index()
    {
        // BUSCA TODAS AS DISCIPLINAS DO USUÁRIO LOGADO
        $disciplinas = Disciplina::where("id_usuario", Auth::id())->get();
        // RETORNA AS DISCIPLINAS EM FORMATO JSON
        return response()->json($disciplinas);
    }

    // CRIA UMA NOVA DISCIPLINA
    public function store(Request $request)
    {
        // CRIA UMA NOVA INSTÂNCIA DA DISCIPLINA
        $disciplina = new Disciplina();
    
        // DEFINE O ID DO USUÁRIO COMO O USUÁRIO LOGADO
        $disciplina->id_usuario = Auth::id();

        // DEFINE O NOME DA DISCIPLINA VINDO DA REQUISIÇÃO
        $disciplina->nome = $request->nome;
        
        // DEFINE A DESCRIÇÃO DA DISCIPLINA (SE VIER VAZIA, FICA COMO STRING VAZIA)
        $disciplina->descricao = $request->descricao ?? '';
        
        // SALVA A DISCIPLINA NO BANCO DE DADOS
        $disciplina->save();

        // RETORNA RESPOSTA DE SUCESSO COM OS DADOS DA DISCIPLINA CRIADA
        return response()->json([
            'success' => true,
            'message' => 'Disciplina criada com sucesso!',
            'data' => $disciplina
        ], 201);
    }

    // EXIBE UMA DISCIPLINA ESPECÍFICA
    public function show($id)
    {
        // BUSCA A DISCIPLINA PELO ID E VERIFICA SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                            ->where("id_usuario", Auth::id())
                                            ->with('topico.flashcard') // CARREGA OS TÓPICOS E FLASHCARDS RELACIONADOS
                                            ->first();
        
        // RETORNA A DISCIPLINA ENCONTRADA
        return response()->json([
            'success' => true,
            'data' => $disciplina
        ]);
    }

    // ATUALIZA UMA DISCIPLINA EXISTENTE
    public function update(Request $request, $id)
    {
        // BUSCA A DISCIPLINA PELO ID E VERIFICA SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                            ->where("id_usuario", Auth::id())
                                            ->first();

        // ATUALIZA O NOME DA DISCIPLINA
        $disciplina->nome = $request->nome;
        
        // ATUALIZA A DESCRIÇÃO DA DISCIPLINA
        $disciplina->descricao = $request->descricao;
        
        // SALVA AS ALTERAÇÕES NO BANCO DE DADOS
        $disciplina->save();

        // RETORNA RESPOSTA DE SUCESSO COM OS DADOS ATUALIZADOS
        return response()->json([
            'success' => true,
            'message' => 'Disciplina atualizada com sucesso!',
            'data' => $disciplina
        ]);
    }

    // REMOVE UMA DISCIPLINA
    public function destroy($id)
    {
        // BUSCA A DISCIPLINA PELO ID E VERIFICA SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                            ->where("id_usuario", Auth::id())
                                            ->first();
        
        // DELETA A DISCIPLINA DO BANCO DE DADOS
        $disciplina->delete();

        // RETORNA RESPOSTA DE SUCESSO
        return response()->json([
            'success' => true,
            'message' => 'Disciplina excluída com sucesso!'
        ]);
    }
}