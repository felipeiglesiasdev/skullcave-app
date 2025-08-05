<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use App\Models\Disciplina; // MODELO DA DISCIPLINA
use App\Models\Topico; // MODELO DO TÓPICO
use App\Models\Flashcard; // MODELO DO FLASHCARD
use App\Models\PerguntaFlashcard; // MODELO DA PERGUNTA FLASHCARD

// DECLARAÇÃO DA CLASSE DO CONTROLLER
class DashboardIndependenteController extends Controller
{
    // ===================================================================================
    // MÉTODO PARA EXIBIR O DASHBOARD ESPECÍFICO PARA USUÁRIOS INDEPENDENTES
    public function index()
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();
        
        // BUSCA AS DISCIPLINAS DO USUÁRIO INDEPENDENTE
        // INCLUI TÓPICOS, FLASHCARDS E PERGUNTAS ASSOCIADAS (EAGER LOADING)
        // ORDENA PELO MAIS RECENTE
        // OBTÉM TODOS OS RESULTADOS
        $disciplinas = Disciplina::where("id_usuario", $user->id_usuario)
            ->with(["topicos.flashcards.perguntas"])
            ->orderBy("created_at", "desc")
            ->get();

        // CALCULA O TOTAL DE TÓPICOS
        $totalTopicos = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->count();
        });

        // CALCULA O TOTAL DE FLASHCARDS
        $totalFlashcards = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->count();
            });
        });

        // CALCULA O TOTAL DE PERGUNTAS
        $totalPerguntas = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->sum(function ($flashcard) {
                    return $flashcard->perguntas->count();
                });
            });
        });

        // RETORNA A VIEW DO DASHBOARD INDEPENDENTE COM OS DADOS
        return view("dashboard.independente", compact(
            "disciplinas",
            "totalTopicos",
            "totalFlashcards",
            "totalPerguntas"
        ));
    }
    // ===================================================================================

    // API: MÉTODO PARA BUSCAR DISCIPLINAS DO USUÁRIO INDEPENDENTE
    public function getDisciplinas()
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();
        
        // BUSCA AS DISCIPLINAS DO USUÁRIO INDEPENDENTE
        // INCLUI TÓPICOS, FLASHCARDS E PERGUNTAS ASSOCIADAS (EAGER LOADING)
        // ORDENA PELO MAIS RECENTE
        // OBTÉM TODOS OS RESULTADOS
        $disciplinas = Disciplina::where("id_usuario", $user->id_usuario)
            ->with(["topicos.flashcards.perguntas"])
            ->orderBy("created_at", "desc")
            ->get();

        // RETORNA UMA RESPOSTA JSON COM SUCESSO E AS DISCIPLINAS
        return response()->json([
            "success" => true,
            "disciplinas" => $disciplinas
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA BUSCAR TÓPICOS DE UMA DISCIPLINA ESPECÍFICA
    public function getTopicos($disciplinaId)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();
        
        // VERIFICA SE A DISCIPLINA PERTENCE AO USUÁRIO AUTENTICADO
        // BUSCA A DISCIPLINA PELO ID E ID DO USUÁRIO
        // OBTÉM O PRIMEIRO RESULTADO
        $disciplina = Disciplina::where("id_disciplina", $disciplinaId)
            ->where("id_usuario", $user->id_usuario)
            ->first();

        // SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
        if (!$disciplina) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Disciplina não encontrada ou sem permissão"
            ], 404);
        }

        // BUSCA OS TÓPICOS DA DISCIPLINA ESPECÍFICA
        // INCLUI FLASHCARDS E PERGUNTAS ASSOCIADAS (EAGER LOADING)
        // ORDENA PELO MAIS RECENTE
        // OBTÉM TODOS OS RESULTADOS
        $topicos = Topico::where("id_disciplina", $disciplinaId)
            ->with(["flashcards.perguntas"])
            ->orderBy("created_at", "desc")
            ->get();

        // RETORNA UMA RESPOSTA JSON COM SUCESSO, OS TÓPICOS E A DISCIPLINA
        return response()->json([
            "success" => true,
            "topicos" => $topicos,
            "disciplina" => $disciplina
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA BUSCAR FLASHCARDS DE UM TÓPICO ESPECÍFICO
    public function getFlashcards($topicoId)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();
        
        // VERIFICA SE O TÓPICO PERTENCE A UMA DISCIPLINA DO USUÁRIO AUTENTICADO
        // USA whereHas PARA VERIFICAR O RELACIONAMENTO COM DISCIPLINA E USUÁRIO
        // BUSCA O TÓPICO PELO ID
        // OBTÉM O PRIMEIRO RESULTADO
        $topico = Topico::whereHas("disciplina", function ($query) use ($user) {
            $query->where("id_usuario", $user->id_usuario);
        })->where("id_topico", $topicoId)->first();

        // SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$topico) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Tópico não encontrado ou sem permissão"
            ], 404);
        }

        // BUSCA OS FLASHCARDS DO TÓPICO ESPECÍFICO
        // INCLUI PERGUNTAS ASSOCIADAS (EAGER LOADING)
        // ORDENA PELO MAIS RECENTE
        // OBTÉM TODOS OS RESULTADOS
        $flashcards = Flashcard::where("id_topico", $topicoId)
            ->with(["perguntas"])
            ->orderBy("created_at", "desc")
            ->get();

        // RETORNA UMA RESPOSTA JSON COM SUCESSO, OS FLASHCARDS E O TÓPICO
        return response()->json([
            "success" => true,
            "flashcards" => $flashcards,
            "topico" => $topico
        ]);
    }

    // ===================================================================================
    // MÉTODO PARA OBTER ESTATÍSTICAS GERAIS DO USUÁRIO INDEPENDENTE
    public function getEstatisticas()
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();
        
        // BUSCA TODAS AS DISCIPLINAS DO USUÁRIO
        $disciplinas = Disciplina::where("id_usuario", $user->id_usuario)->get();
        
        // CONTA O TOTAL DE DISCIPLINAS
        $totalDisciplinas = $disciplinas->count();
        
        // CONTA O TOTAL DE TÓPICOS ASSOCIADOS ÀS DISCIPLINAS DO USUÁRIO
        $totalTopicos = Topico::whereIn("id_disciplina", $disciplinas->pluck("id_disciplina"))->count();
        
        // CONTA O TOTAL DE FLASHCARDS ASSOCIADOS AOS TÓPICOS DAS DISCIPLINAS DO USUÁRIO
        $totalFlashcards = Flashcard::whereHas("topico", function ($query) use ($disciplinas) {
            $query->whereIn("id_disciplina", $disciplinas->pluck("id_disciplina"));
        })->count();

        // CONTA O TOTAL DE PERGUNTAS ASSOCIADAS AOS FLASHCARDS DOS TÓPICOS DAS DISCIPLINAS DO USUÁRIO
        $totalPerguntas = PerguntaFlashcard::whereHas("flashcard.topico", function ($query) use ($disciplinas) {
            $query->whereIn("id_disciplina", $disciplinas->pluck("id_disciplina"));
        })->count();

        // BUSCA AS 5 DISCIPLINAS MAIS RECENTES DO USUÁRIO
        // INCLUI TÓPICOS ASSOCIADOS
        // ORDENA PELO MAIS RECENTE
        // LIMITA A 5 RESULTADOS
        // OBTÉM TODOS OS RESULTADOS
        $disciplinasRecentes = Disciplina::where("id_usuario", $user->id_usuario)
            ->with(["topicos"])
            ->orderBy("created_at", "desc")
            ->limit(80)
            ->get();

        // CALCULA A ATIVIDADE RECENTE (FLASHCARDS CRIADOS NOS ÚLTIMOS 7 DIAS)
        $flashcardsRecentes = Flashcard::whereHas("topico", function ($query) use ($disciplinas) {
            $query->whereIn("id_disciplina", $disciplinas->pluck("id_disciplina"));
        })->where("created_at", ">=", now()->subDays(7))->count();

        // RETORNA UMA RESPOSTA JSON COM SUCESSO E AS ESTATÍSTICAS
        return response()->json([
            "success" => true,
            "estatisticas" => [
                "total_disciplinas" => $totalDisciplinas,
                "total_topicos" => $totalTopicos,
                "total_flashcards" => $totalFlashcards,
                "total_perguntas" => $totalPerguntas,
                "disciplinas_recentes" => $disciplinasRecentes,
                "flashcards_recentes" => $flashcardsRecentes
            ]
        ]);
    }

    // ===================================================================================
    // MÉTODO PARA CRIAR UMA NOVA DISCIPLINA
    public function criarDisciplina(Request $request)
    {
        // VALIDA OS DADOS DA REQUISIÇÃO
        $request->validate([
            "nome" => "required|string|max:255", // NOME É OBRIGATÓRIO E STRING
            "descricao" => "nullable|string|max:1000" // DESCRIÇÃO É OPCIONAL E STRING
        ]);

        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // CRIA UMA NOVA DISCIPLINA NO BANCO DE DADOS
        $disciplina = Disciplina::create([
            "nome" => $request->nome,
            "descricao" => $request->descricao,
            "id_usuario" => $user->id_usuario // ASSOCIA A DISCIPLINA AO USUÁRIO
        ]);

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM A MENSAGEM E A DISCIPLINA CRIADA
        return response()->json([
            "success" => true,
            "message" => "Disciplina criada com sucesso!",
            "disciplina" => $disciplina
        ]);
    }

    // ===================================================================================
    // MÉTODO PARA CRIAR UM NOVO TÓPICO
    public function criarTopico(Request $request)
    {
        // VALIDA OS DADOS DA REQUISIÇÃO
        $request->validate([
            "nome" => "required|string|max:255", // NOME É OBRIGATÓRIO E STRING
            "descricao" => "nullable|string|max:1000", // DESCRIÇÃO É OPCIONAL E STRING
            "disciplina_id" => "required|exists:disciplina,id_disciplina" // ID DA DISCIPLINA É OBRIGATÓRIO E DEVE EXISTIR
        ]);

        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // VERIFICA SE A DISCIPLINA PERTENCE AO USUÁRIO AUTENTICADO
        $disciplina = Disciplina::where("id_disciplina", $request->disciplina_id)
            ->where("id_usuario", $user->id_usuario)
            ->first();

        // SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
        if (!$disciplina) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Disciplina não encontrada ou sem permissão"
            ], 404);
        }

        // CRIA UM NOVO TÓPICO NO BANCO DE DADOS
        $topico = Topico::create([
            "nome" => $request->nome,
            "descricao" => $request->descricao,
            "id_disciplina" => $request->disciplina_id // ASSOCIA O TÓPICO À DISCIPLINA
        ]);

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM A MENSAGEM E O TÓPICO CRIADO
        return response()->json([
            "success" => true,
            "message" => "Tópico criado com sucesso!",
            "topico" => $topico
        ]);
    }

    // ===================================================================================
    // MÉTODO PARA CRIAR UM NOVO FLASHCARD
    public function criarFlashcard(Request $request)
    {
        // VALIDA OS DADOS DA REQUISIÇÃO
        $request->validate([
            "titulo" => "required|string|max:255", // TÍTULO É OBRIGATÓRIO E STRING
            "descricao" => "nullable|string|max:1000", // DESCRIÇÃO É OPCIONAL E STRING
            "topico_id" => "required|exists:topico,id_topico" // ID DO TÓPICO É OBRIGATÓRIO E DEVE EXISTIR
        ]);

        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // VERIFICA SE O TÓPICO PERTENCE A UMA DISCIPLINA DO USUÁRIO AUTENTICADO
        $topico = Topico::whereHas("disciplina", function ($query) use ($user) {
            $query->where("id_usuario", $user->id_usuario);
        })->where("id_topico", $request->topico_id)->first();

        // SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$topico) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Tópico não encontrado ou sem permissão"
            ], 404);
        }

        // CRIA UM NOVO FLASHCARD NO BANCO DE DADOS
        $flashcard = Flashcard::create([
            "titulo" => $request->titulo,
            "descricao" => $request->descricao,
            "id_topico" => $request->topico_id // ASSOCIA O FLASHCARD AO TÓPICO
        ]);

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM A MENSAGEM E O FLASHCARD CRIADO
        return response()->json([
            "success" => true,
            "message" => "Flashcard criado com sucesso!",
            "flashcard" => $flashcard
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA EXCLUIR UM TÓPICO
    public function excluirTopico($id)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // BUSCA O TÓPICO PELO ID E VERIFICA SE PERTENCE A UMA DISCIPLINA DO USUÁRIO
        $topico = Topico::where("id_topico", $id)
            ->whereHas("disciplina", function ($query) use ($user) {
                $query->where("id_usuario", $user->id_usuario);
            })
            ->first();

        // SE O TÓPICO NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$topico) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Tópico não encontrado ou sem permissão para excluir."
            ], 404);
        }

        // EXCLUI O TÓPICO DO BANCO DE DADOS
        $topico->delete();

        // RETORNA UMA RESPOSTA JSON DE SUCESSO
        return response()->json([
            "success" => true,
            "message" => "Tópico excluído com sucesso!"
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA EXCLUIR UM FLASHCARD
    public function excluirFlashcard($id)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // BUSCA O FLASHCARD PELO ID E VERIFICA SE PERTENCE A UM TÓPICO DE UMA DISCIPLINA DO USUÁRIO
        $flashcard = Flashcard::where("id_flashcard", $id)
            ->whereHas("topico.disciplina", function ($query) use ($user) {
                $query->where("id_usuario", $user->id_usuario);
            })
            ->first();

        // SE O FLASHCARD NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$flashcard) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Flashcard não encontrado ou sem permissão para excluir."
            ], 404);
        }

        // EXCLUI O FLASHCARD DO BANCO DE DADOS
        $flashcard->delete();

        // RETORNA UMA RESPOSTA JSON DE SUCESSO
        return response()->json([
            "success" => true,
            "message" => "Flashcard excluído com sucesso!"
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA EXCLUIR UMA DISCIPLINA
    public function excluirDisciplina($id)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // BUSCA A DISCIPLINA PELO ID E VERIFICA SE PERTENCE AO USUÁRIO
        $disciplina = Disciplina::where("id_disciplina", $id)
            ->where("id_usuario", $user->id_usuario)
            ->first();

        // SE A DISCIPLINA NÃO FOR ENCONTRADA OU NÃO PERTENCER AO USUÁRIO
        if (!$disciplina) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Disciplina não encontrada ou sem permissão para excluir."
            ], 404);
        }

        // EXCLUI A DISCIPLINA DO BANCO DE DADOS (OPERAÇÃO EM CASCATA DELETA TÓPICOS E FLASHCARDS ASSOCIADOS)
        $disciplina->delete();

        // RETORNA UMA RESPOSTA JSON DE SUCESSO
        return response()->json([
            "success" => true,
            "message" => "Disciplina excluída com sucesso!"
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA CRIAR UMA PERGUNTA E RESPOSTA PARA UM FLASHCARD
    public function criarPerguntaFlashcard(Request $request)
    {
        // VALIDA OS DADOS DA REQUISIÇÃO
        $request->validate([
            "id_flashcard" => "required|exists:flashcard,id_flashcard", // ID DO FLASHCARD É OBRIGATÓRIO E DEVE EXISTIR
            "pergunta" => "required|string|max:1000", // PERGUNTA É OBRIGATÓRIA E STRING
            "resposta" => "required|string|max:1000", // RESPOSTA É OBRIGATÓRIA E STRING
        ]);

        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // VERIFICA SE O FLASHCARD PERTENCE A UMA DISCIPLINA DO USUÁRIO AUTENTICADO
        $flashcard = Flashcard::where("id_flashcard", $request->id_flashcard)
            ->whereHas("topico.disciplina", function ($query) use ($user) {
                $query->where("id_usuario", $user->id_usuario);
            })
            ->first();

        // SE O FLASHCARD NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$flashcard) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Flashcard não encontrado ou sem permissão para adicionar pergunta."
            ], 404);
        }

        // CRIA UMA NOVA PERGUNTA E RESPOSTA NO BANCO DE DADOS
        $perguntaFlashcard = PerguntaFlashcard::create([
            "id_flashcard" => $request->id_flashcard,
            "pergunta" => $request->pergunta,
            "resposta" => $request->resposta,
        ]);

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM A MENSAGEM E A PERGUNTA CRIADA
        return response()->json([
            "success" => true,
            "message" => "Pergunta e resposta criadas com sucesso!",
            "perguntaFlashcard" => $perguntaFlashcard
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA EDITAR UM FLASHCARD (TÍTULO, DESCRIÇÃO E PERGUNTAS/RESPOSTAS)
    public function editarFlashcard(Request $request, $id)
    {
        // VALIDA OS DADOS DA REQUISIÇÃO
        $request->validate([
            "titulo" => "required|string|max:255", // TÍTULO É OBRIGATÓRIO E STRING
            "descricao" => "nullable|string|max:1000", // DESCRIÇÃO É OPCIONAL E STRING
            "perguntas" => "array", // PERGUNTAS DEVE SER UM ARRAY
            "perguntas.*.id_pergunta_flashcard" => "nullable|exists:pergunta_flashcard,id_pergunta_flashcard", // ID DA PERGUNTA É OPCIONAL E DEVE EXISTIR SE FOR FORNECIDO
            "perguntas.*.pergunta" => "required|string|max:1000", // PERGUNTA É OBRIGATÓRIA E STRING
            "perguntas.*.resposta" => "required|string|max:1000", // RESPOSTA É OBRIGATÓRIA E STRING
        ]);

        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // BUSCA O FLASHCARD PELO ID E VERIFICA SE PERTENCE A UM TÓPICO DE UMA DISCIPLINA DO USUÁRIO
        $flashcard = Flashcard::where("id_flashcard", $id)
            ->whereHas("topico.disciplina", function ($query) use ($user) {
                $query->where("id_usuario", $user->id_usuario);
            })
            ->first();

        // SE O FLASHCARD NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$flashcard) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Flashcard não encontrado ou sem permissão para editar."
            ], 404);
        }

        // ATUALIZA O TÍTULO E A DESCRIÇÃO DO FLASHCARD
        $flashcard->titulo = $request->titulo;
        $flashcard->descricao = $request->descricao;
        $flashcard->save(); // SALVA AS ALTERAÇÕES NO FLASHCARD

        // OBTÉM OS IDS DAS PERGUNTAS EXISTENTES PARA CONTROLE DE EXCLUSÃO
        $existingPerguntaIds = $flashcard->perguntas->pluck("id_pergunta_flashcard")->toArray();
        $updatedPerguntaIds = []; // ARRAY PARA ARMAZENAR IDS DAS PERGUNTAS ATUALIZADAS/CRIADAS

        // PROCESSA AS PERGUNTAS E RESPOSTAS (CRIAR OU ATUALIZAR)
        if ($request->has("perguntas")) {
            foreach ($request->perguntas as $perguntaData) {
                // VERIFICA SE A PERGUNTA JÁ EXISTE (TEM ID)
                if (isset($perguntaData["id_pergunta_flashcard"]) && $perguntaData["id_pergunta_flashcard"]) {
                    // ATUALIZA UMA PERGUNTA EXISTENTE
                    $pergunta = PerguntaFlashcard::where("id_pergunta_flashcard", $perguntaData["id_pergunta_flashcard"])
                        ->where("id_flashcard", $flashcard->id_flashcard)
                        ->first();

                    // SE A PERGUNTA EXISTIR, ATUALIZA SEUS DADOS
                    if ($pergunta) {
                        $pergunta->pergunta = $perguntaData["pergunta"];
                        $pergunta->resposta = $perguntaData["resposta"];
                        $pergunta->save(); // SALVA AS ALTERAÇÕES NA PERGUNTA
                        $updatedPerguntaIds[] = $pergunta->id_pergunta_flashcard; // ADICIONA O ID À LISTA DE ATUALIZADOS
                    }
                } else {
                    // CRIA UMA NOVA PERGUNTA
                    $pergunta = PerguntaFlashcard::create([
                        "id_flashcard" => $flashcard->id_flashcard,
                        "pergunta" => $perguntaData["pergunta"],
                        "resposta" => $perguntaData["resposta"],
                    ]);
                    $updatedPerguntaIds[] = $pergunta->id_pergunta_flashcard; // ADICIONA O ID DA NOVA PERGUNTA
                }
            }
        }

        // IDENTIFICA E EXCLUI PERGUNTAS QUE NÃO FORAM INCLUÍDAS NA REQUISIÇÃO (REMOVIDAS PELO USUÁRIO)
        $perguntasToDelete = array_diff($existingPerguntaIds, $updatedPerguntaIds);
        if (!empty($perguntasToDelete)) {
            PerguntaFlashcard::whereIn("id_pergunta_flashcard", $perguntasToDelete)->delete();
        }

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM A MENSAGEM E O FLASHCARD ATUALIZADO
        return response()->json([
            "success" => true,
            "message" => "Flashcard atualizado com sucesso!",
            "flashcard" => $flashcard->load("perguntas") // RECARREGA AS PERGUNTAS PARA INCLUIR NA RESPOSTA
        ]);
    }

    // ===================================================================================
    // API: MÉTODO PARA BUSCAR UM FLASHCARD ESPECÍFICO COM SUAS PERGUNTAS
    public function getFlashcard($id)
    {
        // OBTÉM O USUÁRIO AUTENTICADO
        $user = Auth::user();

        // BUSCA O FLASHCARD PELO ID E VERIFICA SE PERTENCE A UM TÓPICO DE UMA DISCIPLINA DO USUÁRIO
        // INCLUI AS PERGUNTAS ASSOCIADAS (EAGER LOADING)
        $flashcard = Flashcard::where("id_flashcard", $id)
            ->whereHas("topico.disciplina", function ($query) use ($user) {
                $query->where("id_usuario", $user->id_usuario);
            })
            ->with(["perguntas"])
            ->first();

        // SE O FLASHCARD NÃO FOR ENCONTRADO OU NÃO PERTENCER AO USUÁRIO
        if (!$flashcard) {
            // RETORNA UMA RESPOSTA JSON DE ERRO (404 NOT FOUND)
            return response()->json([
                "success" => false,
                "message" => "Flashcard não encontrado ou sem permissão para visualizar."
            ], 404);
        }

        // RETORNA UMA RESPOSTA JSON DE SUCESSO COM OS DADOS DO FLASHCARD
        return response()->json([
            "success" => true,
            "flashcard" => $flashcard
        ]);
    }


    
}
