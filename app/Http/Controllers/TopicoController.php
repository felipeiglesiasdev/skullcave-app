<?php

namespace App\Http\Controllers;

use App\Models\Topico;
use App\Models\Disciplina;
use App\Models\Independente;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TopicoController extends Controller
{
    /**
     * LISTA TODOS OS TÓPICOS DE UMA DISCIPLINA ESPECÍFICA
     * VERIFICA SE A DISCIPLINA PERTENCE AO USUÁRIO AUTENTICADO
     */
    public function index($disciplinaId)
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

        // VERIFICAR SE A DISCIPLINA EXISTE E PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $disciplinaId)
                                ->where("id_usuario", $user->id_usuario)
                                ->first();

        // SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
        if (!$disciplina) {
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão de acesso'
            ], 404);
        }

        try {
            // BUSCAR TODOS OS TÓPICOS DA DISCIPLINA COM SEUS RELACIONAMENTOS
            $topicos = $disciplina->topicos()
                                  ->with(['flashcards.perguntas']) // CARREGAR FLASHCARDS E PERGUNTAS RELACIONADOS
                                  ->orderBy('created_at', 'desc') // ORDENAR POR DATA DE CRIAÇÃO (MAIS RECENTES PRIMEIRO)
                                  ->get();

            // RETORNAR OS TÓPICOS EM FORMATO JSON COM ESTRUTURA PADRONIZADA
            return response()->json([
                'success' => true,
                'data' => $topicos,
                'total' => $topicos->count(),
                'disciplina' => [
                    'id' => $disciplina->id_disciplina,
                    'nome' => $disciplina->nome
                ]
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO BUSCAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar tópicos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * CRIA UM NOVO TÓPICO PARA UMA DISCIPLINA ESPECÍFICA
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
            'descricao' => 'nullable|string|max:1000', // DESCRIÇÃO É OPCIONAL, STRING, MÁXIMO 1000 CARACTERES
            'disciplina_id' => 'required|integer|exists:disciplina,id_disciplina' // ID DA DISCIPLINA É OBRIGATÓRIO E DEVE EXISTIR
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

        // VERIFICAR SE A DISCIPLINA EXISTE E PERTENCE AO USUÁRIO LOGADO
        $disciplina = Disciplina::where("id_disciplina", $request->disciplina_id)
                                ->where("id_usuario", $user->id_usuario)
                                ->first();

        // SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
        if (!$disciplina) {
            return response()->json([
                'success' => false,
                'message' => 'Disciplina não encontrada ou sem permissão de acesso'
            ], 404);
        }

        // VERIFICAR SE JÁ EXISTE UM TÓPICO COM O MESMO NOME NESTA DISCIPLINA
        $topicoExistente = Topico::where('id_disciplina', $request->disciplina_id)
                                 ->where('nome', $request->nome)
                                 ->first();
        
        // SE JÁ EXISTE, RETORNAR ERRO
        if ($topicoExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um tópico com este nome nesta disciplina'
            ], 409);
        }

        try {
            // CRIAR UM NOVO TÓPICO COM OS DADOS VALIDADOS
            $topico = Topico::create([
                "id_disciplina" => $request->disciplina_id,
                "nome" => $request->nome,
                "descricao" => $request->descricao ?? '',
            ]);

            // CARREGAR RELACIONAMENTOS PARA RETORNAR DADOS COMPLETOS
            $topico->load(['disciplina', 'flashcards.perguntas']);

            // RETORNAR RESPOSTA DE SUCESSO COM OS DADOS DO TÓPICO CRIADO
            return response()->json([
                'success' => true,
                'message' => 'Tópico criado com sucesso!',
                'data' => $topico
            ], 201);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO SALVAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao criar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * EXIBE UM TÓPICO ESPECÍFICO COM TODOS OS SEUS RELACIONAMENTOS
     * VERIFICA SE O TÓPICO PERTENCE AO USUÁRIO LOGADO
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

        try {
            // BUSCAR O TÓPICO PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
            $topico = Topico::where("id_topico", $id)
                            ->whereHas("disciplina", function ($query) use ($user) {
                                // VERIFICAR SE A DISCIPLINA PERTENCE AO USUÁRIO LOGADO
                                $query->where("id_usuario", $user->id_usuario);
                            })
                            ->with(['disciplina', 'flashcards.perguntas']) // CARREGAR RELACIONAMENTOS COMPLETOS
                            ->first();
            
            // VERIFICAR SE O TÓPICO FOI ENCONTRADO
            if (!$topico) {
                // RETORNAR ERRO SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
                return response()->json([
                    'success' => false,
                    'message' => 'Tópico não encontrado ou sem permissão de acesso'
                ], 404);
            }
            
            // RETORNAR O TÓPICO ENCONTRADO COM TODOS OS DADOS
            return response()->json([
                'success' => true,
                'data' => $topico
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO BUSCAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao buscar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ATUALIZA UM TÓPICO EXISTENTE
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

        try {
            // BUSCAR O TÓPICO PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
            $topico = Topico::where("id_topico", $id)
                            ->whereHas("disciplina", function ($query) use ($user) {
                                // VERIFICAR SE A DISCIPLINA PERTENCE AO USUÁRIO LOGADO
                                $query->where("id_usuario", $user->id_usuario);
                            })
                            ->first();

            // VERIFICAR SE O TÓPICO FOI ENCONTRADO
            if (!$topico) {
                // RETORNAR ERRO SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
                return response()->json([
                    'success' => false,
                    'message' => 'Tópico não encontrado ou sem permissão de acesso'
                ], 404);
            }

            // VERIFICAR SE JÁ EXISTE OUTRO TÓPICO COM O MESMO NOME NESTA DISCIPLINA
            $topicoExistente = Topico::where('id_disciplina', $topico->id_disciplina)
                                     ->where('nome', $request->nome)
                                     ->where('id_topico', '!=', $id) // EXCLUIR O TÓPICO ATUAL DA VERIFICAÇÃO
                                     ->first();
            
            // SE JÁ EXISTE, RETORNAR ERRO
            if ($topicoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe outro tópico com este nome nesta disciplina'
                ], 409);
            }

            // ATUALIZAR OS DADOS DO TÓPICO
            $topico->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao ?? '',
            ]);

            // CARREGAR RELACIONAMENTOS PARA RETORNAR DADOS COMPLETOS
            $topico->load(['disciplina', 'flashcards.perguntas']);

            // RETORNAR RESPOSTA DE SUCESSO COM OS DADOS ATUALIZADOS
            return response()->json([
                'success' => true,
                'message' => 'Tópico atualizado com sucesso!',
                'data' => $topico
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO ATUALIZAR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao atualizar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * REMOVE UM TÓPICO E TODOS OS SEUS RELACIONAMENTOS
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

        try {
            // BUSCAR O TÓPICO PELO ID E VERIFICAR SE PERTENCE AO USUÁRIO LOGADO
            $topico = Topico::where("id_topico", $id)
                            ->whereHas("disciplina", function ($query) use ($user) {
                                // VERIFICAR SE A DISCIPLINA PERTENCE AO USUÁRIO LOGADO
                                $query->where("id_usuario", $user->id_usuario);
                            })
                            ->with(['flashcards.perguntas']) // CARREGAR RELACIONAMENTOS PARA CONTAGEM
                            ->first();

            // VERIFICAR SE O TÓPICO FOI ENCONTRADO
            if (!$topico) {
                // RETORNAR ERRO SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
                return response()->json([
                    'success' => false,
                    'message' => 'Tópico não encontrado ou sem permissão de acesso'
                ], 404);
            }

            // CONTAR QUANTOS FLASHCARDS E PERGUNTAS SERÃO EXCLUÍDOS JUNTO
            $totalFlashcards = $topico->flashcards->count();
            $totalPerguntas = $topico->flashcards->sum(function($flashcard) {
                return $flashcard->perguntas->count();
            });
            
            // DELETAR O TÓPICO DO BANCO DE DADOS
            // OS RELACIONAMENTOS SERÃO EXCLUÍDOS EM CASCATA CONFORME DEFINIDO NAS MIGRATIONS
            $topico->delete();

            // RETORNAR RESPOSTA DE SUCESSO COM INFORMAÇÕES SOBRE O QUE FOI EXCLUÍDO
            return response()->json([
                'success' => true,
                'message' => 'Tópico excluído com sucesso!',
                'deleted_items' => [
                    'topico' => 1,
                    'flashcards' => $totalFlashcards,
                    'perguntas' => $totalPerguntas
                ]
            ]);

        } catch (\Exception $e) {
            // SE HOUVER ERRO AO EXCLUIR, RETORNAR ERRO INTERNO
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor ao excluir tópico',
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