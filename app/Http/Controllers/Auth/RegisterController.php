<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Independente;

/**
 * @CLASS REGISTERCONTROLLER
 * @BRIEF RESPONSÁVEL POR TODAS AS OPERAÇÕES RELACIONADAS AO PROCESSO DE CADASTRO DE USUÁRIOS E ESCOLAS.
 *
 * ESTE CONTROLLER GERENCIA A EXIBIÇÃO DO FORMULÁRIO DE CADASTRO, A VALIDAÇÃO DOS DADOS
 * E A CRIAÇÃO DE NOVOS REGISTROS, SEJA PARA USUÁRIOS INDIVIDUAIS (INDEPENDENTE) OU
 * PARA O REGISTRO DE INTERESSE DE ESCOLAS.
 */
class RegisterController extends Controller
{
    /**
     * @BRIEF EXIBE O FORMULÁRIO DE CADASTRO PARA O USUÁRIO.
     *
     * ESTE MÉTODO SIMPLESMENTE RETORNA A VIEW `AUTH.REGISTER`, QUE CONTÉM O FORMULÁRIO
     * HTML PARA QUE O USUÁRIO ESCOLHA O TIPO DE CADASTRO (ACESSO INDIVIDUAL OU ESCOLA)
     * E INSIRA OS DADOS CORRESPONDENTES.
     *
     * @RETURN \ILLUMINATE\VIEW\VIEW A VIEW DO FORMULÁRIO DE CADASTRO.
     */
    public function showRegistrationForm()
    {
        return view("auth.register");
    }

    /**
     * @BRIEF PROCESSA A REQUISIÇÃO DE CADASTRO, DIRECIONANDO PARA O MÉTODO APROPRIADO.
     *
     * REALIZA UMA VALIDAÇÃO BÁSICA INICIAL PARA DETERMINAR O TIPO DE CADASTRO (INDEPENDENTE OU ESCOLA)
     * E, EM SEGUIDA, CHAMA O MÉTODO PRIVADO ESPECÍFICO PARA LIDAR COM A LÓGICA DE CADA TIPO.
     *
     * @PARAM \ILLUMINATE\HTTP\REQUEST $REQUEST O OBJETO REQUEST CONTENDO OS DADOS DO FORMULÁRIO.
     * @RETURN \ILLUMINATE\HTTP\REDIRECTRESPONSE REDIRECIONA PARA O DASHBOARD OU RETORNA COM SUCESSO/ERRO.
     */
    public function register(Request $request)
    {
        // ====================== 1. VALIDAÇÃO BÁSICA DO TIPO DE CADASTRO ======================
        // VALIDA OS CAMPOS ESSENCIAIS PARA DETERMINAR O FLUXO DE CADASTRO.
        // 'NAME': NOME COMPLETO DO SOLICITANTE.
        // 'EMAIL': ENDEREÇO DE E-MAIL.
        // 'USER_TYPE': TIPO DE CADASTRO, DEVE SER 'INDEPENDENTE' OU 'ESCOLA'.
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "user_type" => "required|in:independente,escola",
        ]);

        // ====================== 2. DIRECIONAMENTO BASEADO NO TIPO DE USUÁRIO ======================
        // SE O TIPO DE USUÁRIO FOR 'ESCOLA', CHAMA O MÉTODO PARA REGISTRAR INTERESSE DE ESCOLA.
        // CASO CONTRÁRIO (SE FOR 'INDEPENDENTE'), CHAMA O MÉTODO PARA REGISTRAR USUÁRIO INDIVIDUAL.
        if ($request->user_type === "escola") {
            return $this->registerEscola($request);
        } else {
            return $this->registerIndependente($request);
        }
    }

    /**
     * @BRIEF LIDA COM O CADASTRO DE UM USUÁRIO COM ACESSO INDIVIDUAL (INDEPENDENTE).
     *
     * VALIDA OS DADOS ESPECÍFICOS PARA O CADASTRO INDIVIDUAL, CRIA UM NOVO REGISTRO NA TABELA `USUARIOS`
     * E `INDEPENDENTES`, REALIZA O LOGIN AUTOMÁTICO DO USUÁRIO E O REDIRECIONA PARA O DASHBOARD.
     *
     * @PARAM \ILLUMINATE\HTTP\REQUEST $REQUEST O OBJETO REQUEST CONTENDO OS DADOS DO FORMULÁRIO.
     * @RETURN \ILLUMINATE\HTTP\REDIRECTRESPONSE REDIRECIONA PARA O DASHBOARD EM CASO DE SUCESSO.
     */
    private function registerIndependente(Request $request)
    {
        // ====================== 1. VALIDAÇÃO ESPECÍFICA PARA CADASTRO INDIVIDUAL ======================
        // VALIDA OS CAMPOS NECESSÁRIOS PARA UM USUÁRIO INDEPENDENTE.
        // 'NAME': NOME COMPLETO (JÁ VALIDADO, MAS REVALIDADO PARA CLAREZA).
        // 'EMAIL': EMAIL ÚNICO NA TABELA 'USUARIOS', PARA EVITAR DUPLICIDADE.
        // 'PASSWORD': SENHA, COM CONFIRMAÇÃO E MÍNIMO DE 8 CARACTERES.
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:usuario,email|max:255",
            "password" => "required|confirmed|min:8",
        ]);

        // ====================== 2. CRIAÇÃO DO USUÁRIO PRINCIPAL ======================
        // CRIA UM NOVO REGISTRO NA TABELA 'USUARIOS' COM OS DADOS FORNECIDOS.
        // A SENHA É CRIPTOGRAFADA USANDO BCRYPT PARA SEGURANÇA.
        $usuario = Usuario::create([
            "nome" => $data["name"],
            "email" => $data["email"],
            "senha" => bcrypt($data["password"]),
            "tipo" => "independente", // DEFINE O TIPO DE USUÁRIO COMO 'INDEPENDENTE'.
            "data_cadastro" => now(), // REGISTRA A DATA E HORA ATUAIS DO CADASTRO.
        ]);

        // ====================== 3. CRIAÇÃO DO PERFIL INDEPENDENTE ======================
        // CRIA UM REGISTRO ASSOCIADO NA TABELA 'INDEPENDENTES', USANDO O ID DO USUÁRIO RECÉM-CRIADO.
        Independente::create([
            "id_usuario" => $usuario->id_usuario,
        ]);

        // ====================== 4. AUTENTICAÇÃO AUTOMÁTICA APÓS CADASTRO ======================
        // LOGA O USUÁRIO RECÉM-CRIADO AUTOMATICAMENTE NO SISTEMA.
        Auth::login($usuario);

        // ====================== 5. REDIRECIONAMENTO PARA DASHBOARD ======================
        // REDIRECIONA O USUÁRIO PARA A PÁGINA DO DASHBOARD COM UMA MENSAGEM DE SUCESSO.
        return redirect("/dashboard")->with("success", "Cadastro realizado com sucesso!");
    }

    /**
     * @BRIEF LIDA COM O REGISTRO DE INTERESSE DE UMA ESCOLA.
     *
     * VALIDA OS DADOS ESPECÍFICOS PARA O REGISTRO DE ESCOLA E INSERE AS INFORMAÇÕES
     * NA TABELA `INTERESSE_ESCOLAS`. NÃO CRIA UM USUÁRIO NO SISTEMA, APENAS REGISTRA
     * O INTERESSE PARA CONTATO FUTURO.
     *
     * @PARAM \ILLUMINATE\HTTP\REQUEST $REQUEST O OBJETO REQUEST CONTENDO OS DADOS DO FORMULÁRIO.
     * @RETURN \ILLUMINATE\HTTP\REDIRECTRESPONSE REDIRECIONA DE VOLTA COM UMA FLAG DE SUCESSO PARA EXIBIR O MODAL.
     */
    private function registerEscola(Request $request)
    {
        // ====================== 1. VALIDAÇÃO ESPECÍFICA PARA CADASTRO DE ESCOLA ======================
        // VALIDA OS CAMPOS NECESSÁRIOS PARA O REGISTRO DE INTERESSE DE ESCOLA.
        // 'NAME': NOME DO RESPONSÁVEL.
        // 'EMAIL': EMAIL DO RESPONSÁVEL.
        // 'CARGO': CARGO QUE O RESPONSÁVEL OCUPA NA ESCOLA.
        // 'ESCOLA_NOME': NOME DA ESCOLA.
        // 'ESCOLA_CNPJ': CNPJ DA ESCOLA (MÁXIMO 18 CARACTERES PARA INCLUIR FORMATAÇÃO).
        // 'TELEFONE': TELEFONE DE CONTATO (MÁXIMO 20 CARACTERES PARA INCLUIR FORMATAÇÃO).
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "cargo" => "required|string|max:255",
            "escola_nome" => "required|string|max:255",
            "escola_cnpj" => "required|string|max:18",
            "telefone" => "required|string|max:20",
        ]);

        // ====================== 2. INSERÇÃO NA TABELA INTERESSE_ESCOLAS ======================
        // INSERE OS DADOS VALIDADOS DIRETAMENTE NA TABELA 'INTERESSE_ESCOLAS'.
        DB::table("interesse_escolas")->insert([
            "nome" => $data["name"],
            "email" => $data["email"],
            "cargo" => $data["cargo"],
            "nome_escola" => $data["escola_nome"],
            "cnpj" => $data["escola_cnpj"],
            "telefone" => $data["telefone"],
            "data_interesse" => now(), // REGISTRA A DATA E HORA ATUAIS DO INTERESSE.
        ]);

        // ====================== 3. RETORNO COM SUCESSO (PARA EXIBIR MODAL) ======================
        // REDIRECIONA DE VOLTA PARA A PÁGINA ANTERIOR COM UMA FLAG DE SESSÃO 'ESCOLA_SUCCESS'.
        // ESTA FLAG SERÁ USADA NA VIEW PARA DISPARAR A EXIBIÇÃO DE UM MODAL DE SUCESSO.
        return back()->with("escola_success", true);
    }
}