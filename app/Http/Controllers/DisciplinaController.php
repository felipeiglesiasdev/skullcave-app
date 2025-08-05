<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Independente;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DisciplinaController extends Controller
{
    /**
     * LISTA TODAS AS DISCIPLINAS DO USUÁRIO AUTENTICADO
     * ESTE MÉTODO FUNCIONA PARA USUÁRIOS INDEPENDENTES E ALUNOS
     * PARA PROFESSORES, USAR O MÉTODO ESPECÍFICO getDisciplinasPorTurma()
     */
    public function index()
    {
        // OBTER O USUÁRIO AUTENTICADO ATUAL
        $user = Auth::user();
        
        // VERIFICAR SE O USUÁRIO É VÁLIDO E ESTÁ LOGADO
        if (!$user) {
            // RETORNAR ERRO SE NÃO HOUVER USUÁRIO LOGADO
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // BUSCAR TODAS AS DISCIPLINAS QUE PERTENCEM AO USUÁRIO LOGADO
        $disciplinas = Disciplina::where("id_usuario", $user->id_usuario)
                                ->with(['topicos.flashcards.perguntas']) // CARREGAR RELACIONAMENTOS ANINHADOS
                                ->orderBy('created_at', 'desc') // ORDENAR POR DATA DE CRIAÇÃO (MAIS RECENTES PRIMEIRO)
                                ->get();
        
        // RETORNAR AS DISCIPLINAS EM FORMATO JSON COM ESTRUTURA PADRONIZADA
        return response()->json([
            'success' => true,
            'data' => $disciplinas,
            'total' => $disciplinas->count()
        ]);
    }

    /**
     * CRIA UMA NOVA DISCIPLINA PARA O USUÁRIO AUTENTICADO
     * VALIDA OS DADOS DE ENTRADA E VERIFICA PERMISSÕES
     */
    public function store(Request $request)
    {
        // OBTER O USUÁRIO AUTENTICADO ATUAL
        $user = Auth::user();
        
        // VERIFICAR SE O USUÁRIO É VÁLIDO E ESTÁ LOGADO
        if (!$user) {
            // RETORNAR ERRO SE NÃO HOUVER USUÁRIO LOGADO
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // DEFINIR REGRAS DE VALIDAÇÃO PARA OS DADOS DE ENTRADA
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|min:3', // NOME É OBRIGATÓRIO, STRING, MÁXIMO 255 CARACTERES, MÍNIMO 3
            'descricao' => 'nullable|string|max:1000' // DESCRIÇÃO É OPCIONAL, STRING, MÁXIMO 1000 CARACTERES
        ]);

        // VERIFICAR SE A VALIDAÇÃO FALHOU
        if ($validator->fails()) {
            // RETORNAR ERROS DE VALIDAÇÃO
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // VERIFICAR SE JÁ EXISTE UMA DISCIPLINA COM O MESMO NOME PARA ESTE USUÁRIO
        $disciplinaExistente = Disciplina::where('id_usuario', $user->id_usuario)
                                        ->where('nome', $request->nome)
                                        ->first();
        
        // SE JÁ EXISTE, RETORNAR ERRO
        if ($disciplinaExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma disciplina com este nome'
            ], 409);
        }

        // CRIAR UMA NOVA INSTÂNCIA DA DISCIPLINA
        $disciplina = new Disciplina();
    
        // DEFINIR O ID DO USUÁRIO COMO O USUÁRIO LOGADO
        $disciplina->id_usuario = $user->id_usuario;

        // DEFINIR O NOME DA DISCIPLINA VINDO DA REQUISIÇÃO
        $disciplina->nome = $request->nome;
        
        // DEFINIR A DESCRIÇÃO DA DISCIPLINA (SE VIER VAZIA, FICA COMO STRING VAZIA)
        $disciplina->descricao = $request->descricao ?? '';
        
        try {
            // TENTAR SALVAR A DISCIPLINA NO BANCO DE DADOS
            $disciplina->save();

            // RETORNAR RESPOSTA DE SUCESSO COM OS DADOS DA DISCIPLINA CRIADA
            return response()->json([
                'success' => true,
                'message' => 'Disciplina criada com sucesso!',
                'data' => $disciplina
            ], 201);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO SALVAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao criar disciplina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * EXIBE UMA DISCIPLINA ESPECÍFICA COM TODOS OS SEUS RELACIONAMENTOS
     * VERIFICA SE A DISCIPLINA PERTENCE AO USUÁRIO LOGADO
     */
    public function show($id)
    {
        // OBTER O USUÁRIO AUTENTICADO ATUAL
        $user = Auth::user();
        
        // VERIFICAR SE O USUÁRIO É VÁLIDO E ESTÁ LOGADO
        if (!$user) {
            // RETORNAR ERRO SE NÃO HOUVER USUÁRIO LOGADO
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // BUSCAR A DISCIPLINA PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                ->where("id_usuario", $user->id_usuario)
                                ->with(['topicos.flashcards.perguntas']) // CARREGAR OS TÓPICOS, FLASHCARDS E PERGUNTAS RELACIONADOS
                                ->first();
        
        // VERIFICAR SE A DISCIPLINA FOI ENCONTRADA
        if (!$disciplina) {
            // RETORNAR ERRO SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão de acesso'
            ], 404);
        }
        
        // RETORNAR A DISCIPLINA ENCONTRADA COM TODOS OS DADOS
        return response()->json([
            'success' => true,
            'data' => $disciplina
        ]);
    }

    /**
     * ATUALIZA UMA DISCIPLINA EXISTENTE
     * VALIDA OS DADOS E VERIFICA PERMISSÕES DE PROPRIEDADE
     */
    public function update(Request $request, $id)
    {
        // OBTER O USUÁRIO AUTENTICADO ATUAL
        $user = Auth::user();
        
        // VERIFICAR SE O USUÁRIO É VÁLIDO E ESTÁ LOGADO
        if (!$user) {
            // RETORNAR ERRO SE NÃO HOUVER USUÁRIO LOGADO
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // DEFINIR REGRAS DE VALIDAÇÃO PARA OS DADOS DE ENTRADA
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|min:3', // NOME É OBRIGATÓRIO, STRING, MÁXIMO 255 CARACTERES, MÍNIMO 3
            'descricao' => 'nullable|string|max:1000' // DESCRIÇÃO É OPCIONAL, STRING, MÁXIMO 1000 CARACTERES
        ]);

        // VERIFICAR SE A VALIDAÇÃO FALHOU
        if ($validator->fails()) {
            // RETORNAR ERROS DE VALIDAÇÃO
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // BUSCAR A DISCIPLINA PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                ->where("id_usuario", $user->id_usuario)
                                ->first();

        // VERIFICAR SE A DISCIPLINA FOI ENCONTRADA
        if (!$disciplina) {
            // RETORNAR ERRO SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão de acesso'
            ], 404);
        }

        // VERIFICAR SE JÁ EXISTE OUTRA DISCIPLINA COM O MESMO NOME PARA ESTE USUÁRIO
        $disciplinaExistente = Disciplina::where('id_usuario', $user->id_usuario)
                                        ->where('nome', $request->nome)
                                        ->where('id_disciplina', '!=', $id) // EXCLUIR A DISCIPLINA ATUAL DA VERIFICAÇÃO
                                        ->first();
        
        // SE JÁ EXISTE, RETORNAR ERRO
        if ($disciplinaExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe outra disciplina com este nome'
            ], 409);
        }

        try {
            // ATUALIZAR O NOME DA DISCIPLINA
            $disciplina->nome = $request->nome;
            
            // ATUALIZAR A DESCRIÇÃO DA DISCIPLINA
            $disciplina->descricao = $request->descricao ?? '';
            
            // SALVAR AS ALTERAÇÕES NO BANCO DE DADOS
            $disciplina->save();

            // RETORNAR RESPOSTA DE SUCESSO COM OS DADOS ATUALIZADOS
            return response()->json([
                'success' => true,
                'message' => 'Disciplina atualizada com sucesso!',
                'data' => $disciplina
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO SALVAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao atualizar disciplina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * REMOVE UMA DISCIPLINA E TODOS OS SEUS RELACIONAMENTOS
     * VERIFICA PERMISSÕES E EXECUTA EXCLUSÃO EM CASCATA
     */
    public function destroy($id)
    {
        // OBTER O USUÁRIO AUTENTICADO ATUAL
        $user = Auth::user();
        
        // VERIFICAR SE O USUÁRIO É VÁLIDO E ESTÁ LOGADO
        if (!$user) {
            // RETORNAR ERRO SE NÃO HOUVER USUÁRIO LOGADO
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // BUSCAR A DISCIPLINA PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $id)
                                ->where("id_usuario", $user->id_usuario)
                                ->with(['topicos.flashcards.perguntas']) // CARREGAR RELACIONAMENTOS PARA CONTAGEM
                                ->first();
        
        // VERIFICAR SE A DISCIPLINA FOI ENCONTRADA
        if (!$disciplina) {
            // RETORNAR ERRO SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão de acesso'
            ], 404);
        }

        // CONTAR QUANTOS TÓPICOS E FLASHCARDS SERÃO EXCLUÍDOS JUNTO
        $totalTopicos = $disciplina->topicos->count();
        $totalFlashcards = $disciplina->topicos->sum(function($topico) {
            return $topico->flashcards->count();
        });
        $totalPerguntas = $disciplina->topicos->sum(function($topico) {
            return $topico->flashcards->sum(function($flashcard) {
                return $flashcard->perguntas->count();
            });
        });

        try {
            // DELETAR A DISCIPLINA DO BANCO DE DADOS
            // OS RELACIONAMENTOS SERÃO EXCLUÍDOS EM CASCATA CONFORME DEFINIDO NAS MIGRATIONS
            $disciplina->delete();

            // RETORNAR RESPOSTA DE SUCESSO COM INFORMAÇÕES SOBRE O QUE FOI EXCLUÍDO
            return response()->json([
                'success' => true,
                'message' => 'Disciplina excluída com sucesso!',
                'deleted_items' => [
                    'disciplina' => 1,
                    'topicos' => $totalTopicos,
                    'flashcards' => $totalFlashcards,
                    'perguntas' => $totalPerguntas
                ]
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO EXCLUIR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao excluir disciplina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * MÉTODO AUXILIAR PARA VERIFICAR O TIPO DE USUÁRIO
     * RETORNA O TIPO DE USUÁRIO (INDEPENDENTE, ALUNO, PROFESSOR) OU NULL
     */
    private function getTipoUsuario($userId)
    {
        // VERIFICAR SE É USUÁRIO INDEPENDENTE
        if (Independente::where('id_usuario', $userId)->exists()) {
            return 'independente';
        }
        
        // VERIFICAR SE É ALUNO
        if (Aluno::where('id_usuario', $userId)->exists()) {
            return 'aluno';
        }
        
        // VERIFICAR SE É PROFESSOR
        if (Professor::where('id_usuario', $userId)->exists()) {
            return 'professor';
        }
        
        // RETORNAR NULL SE NÃO FOR NENHUM DOS TIPOS ESPECÍFICOS
        return null;
    }
}