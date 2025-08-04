<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

/**
 * @CLASS LOGINCONTROLLER
 * @BRIEF RESPONSÁVEL POR TODAS AS OPERAÇÕES RELACIONADAS AO PROCESSO DE AUTENTICAÇÃO (LOGIN) DE USUÁRIOS.
 *
 * ESTE CONTROLLER GERENCIA A EXIBIÇÃO DO FORMULÁRIO DE LOGIN, O PROCESSAMENTO DAS CREDENCIAIS
 * E O LOGOUT DOS USUÁRIOS, GARANTINDO QUE APENAS USUÁRIOS COM O TIPO CORRETO POSSAM ACESSAR
 * O DASHBOARD.
 */
class LoginController extends Controller
{
    /**
     * @BRIEF EXIBE O FORMULÁRIO DE LOGIN PARA O USUÁRIO.
     *
     * ESTE MÉTODO SIMPLESMENTE RETORNA A VIEW `AUTH.LOGIN`, QUE CONTÉM O FORMULÁRIO
     * HTML PARA QUE O USUÁRIO INSIRA SUAS CREDENCIAIS (EMAIL, SENHA E TIPO DE USUÁRIO).
     *
     * @RETURN \ILLUMINATE\VIEW\VIEW A VIEW DO FORMULÁRIO DE LOGIN.
     */
    public function showLoginForm()
    {
        return view("auth.login");
    }

    /**
     * @BRIEF PROCESSA A TENTATIVA DE LOGIN DO USUÁRIO.
     *
     * VALIDA AS CREDENCIAIS FORNECIDAS (EMAIL, SENHA E TIPO DE USUÁRIO) E TENTA AUTENTICAR
     * O USUÁRIO. SE AS CREDENCIAIS FOREM VÁLIDAS E O TIPO DE USUÁRIO CORRESPONDER,
     * O USUÁRIO É LOGADO E REDIRECIONADO PARA O DASHBOARD. CASO CONTRÁRIO, RETORNA
     * COM ERROS DE VALIDAÇÃO.
     *
     * @PARAM \ILLUMINATE\HTTP\REQUEST $REQUEST O OBJETO REQUEST CONTENDO OS DADOS DO FORMULÁRIO.
     * @RETURN \ILLUMINATE\HTTP\REDIRECTRESPONSE REDIRECIONA PARA O DASHBOARD EM CASO DE SUCESSO OU DE VOLTA COM ERROS.
     */
    public function login(Request $request)
    {
        // ====================== 1. VALIDAÇÃO DOS CAMPOS DE ENTRADA ======================
        // VALIDA OS CAMPOS 'EMAIL', 'PASSWORD' E 'USER_TYPE' DO FORMULÁRIO.
        // 'EMAIL': DEVE SER OBRIGATÓRIO E TER FORMATO DE EMAIL.
        // 'PASSWORD': DEVE SER OBRIGATÓRIO.
        // 'USER_TYPE': DEVE SER OBRIGATÓRIO E UM DOS VALORES PERMITIDOS (INDEPENDENTE, ALUNO, PROFESSOR).
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
            "user_type" => "required|in:independente,aluno,professor",
        ]);

        // ====================== 2. BUSCA DO USUÁRIO NO BANCO DE DADOS ======================
        // TENTA ENCONTRAR UM USUÁRIO NO BANCO DE DADOS COM O EMAIL FORNECIDO.
        $usuario = Usuario::where("email", $credentials["email"])->first();

        // ====================== 3. VERIFICAÇÃO DE CREDENCIAIS E TIPO DE USUÁRIO ======================
        // VERIFICA SE O USUÁRIO FOI ENCONTRADO, SE A SENHA FORNECIDA CORRESPONDE À SENHA ARMAZENADA
        // (USANDO PASSWORD_VERIFY PARA SENHAS BCRYPT) E SE O TIPO DE USUÁRIO SELECIONADO NO FORMULÁRIO
        // CORRESPONDE AO TIPO DE USUÁRIO REGISTRADO NO BANCO DE DADOS.
        if ($usuario && password_verify($credentials["password"], $usuario->senha) && $usuario->tipo === $credentials["user_type"]) {
            // ====================== 4. LOGIN REALIZADO COM SUCESSO ======================
            // AUTENTICA O USUÁRIO NO SISTEMA LARAVEL.
            Auth::login($usuario);
            // REGENERA A SESSÃO PARA PREVENIR ATAQUES DE FIXAÇÃO DE SESSÃO.
            $request->session()->regenerate();
            
            // REDIRECIONA O USUÁRIO PARA O DASHBOARD (OU PARA A URL QUE ELE TENTOU ACESSAR ANTES DO LOGIN).
            return redirect()->intended("/dashboard");
        }

        // ====================== 5. RETORNO EM CASO DE FALHA ======================
        // SE A AUTENTICAÇÃO FALHAR (CREDENCIAS INVÁLIDAS OU TIPO DE USUÁRIO INCORRETO),
        // REDIRECIONA O USUÁRIO DE VOLTA PARA O FORMULÁRIO DE LOGIN COM UMA MENSAGEM DE ERRO
        // E MANTÉM OS DADOS DE ENTRADA (EXCETO A SENHA) PARA QUE O USUÁRIO NÃO PRECISE DIGITÁ-LOS NOVAMENTE.
        return back()->withErrors([
            "email" => "Credenciais ou tipo de usuário inválidos.",
        ])->withInput();
    }

    /**
     * @BRIEF REALIZA O LOGOUT DO USUÁRIO AUTENTICADO.
     *
     * INVALIDA A SESSÃO DO USUÁRIO, REMOVE O TOKEN DE SESSÃO E REDIRECIONA O USUÁRIO
     * PARA A PÁGINA INICIAL (ROOT).
     *
     * @PARAM \ILLUMINATE\HTTP\REQUEST $REQUEST O OBJETO REQUEST.
     * @RETURN \ILLUMINATE\HTTP\REDIRECTRESPONSE REDIRECIONA PARA A PÁGINA INICIAL.
     */
    public function logout(Request $request)
    {
        // FAZ O LOGOUT DO USUÁRIO ATUALMENTE AUTENTICADO.
        Auth::logout();
        
        // INVALIDA A SESSÃO ATUAL.
        $request->session()->invalidate();
        // REGENERA O TOKEN CSRF DA SESSÃO PARA SEGURANÇA.
        $request->session()->regenerateToken();
        
        // REDIRECIONA O USUÁRIO PARA A PÁGINA INICIAL.
        return redirect("/");
    }

    /**
     * @BRIEF DEFINE O CAMINHO DE REDIRECIONAMENTO PADRÃO APÓS O LOGIN.
     *
     * ESTE MÉTODO É USADO INTERNAMENTE PELO LARAVEL PARA DETERMINAR PARA ONDE REDIRECIONAR
     * O USUÁRIO APÓS UM LOGIN BEM-SUCEDIDO, CASO NÃO HAJA UMA URL 'INTENDED' PRÉVIA.
     *
     * @RETURN STRING O CAMINHO PARA O DASHBOARD.
     */
    protected function redirectTo()
    {
        return "/dashboard";
    }
}