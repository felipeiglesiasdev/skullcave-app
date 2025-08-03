<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

// ====================== LOGINCONTROLLER ======================
// RESPONSÁVEL POR TODAS AS OPERAÇÕES RELACIONADAS AO LOGIN
class LoginController extends Controller
{
    // ====================== MÉTODO PARA MOSTRAR O FORMULÁRIO DE LOGIN ======================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ====================== MÉTODO PARA PROCESSAR O LOGIN ======================
    public function login(Request $request)
    {
        // ====================== VALIDAÇÃO DOS CAMPOS ======================
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // ====================== TENTATIVA DE AUTENTICAÇÃO PERSONALIZADA ======================
        $usuario = Usuario::where('email', $credentials['email'])->first();

        if ($usuario && password_verify($credentials['password'], $usuario->senha)) {
            Auth::login($usuario);
            return redirect()->intended('dashboard');
        }

        // ====================== RETORNO EM CASO DE FALHA ======================
        return back()->withErrors([
            'email' => 'CREDENCIAIS INVÁLIDAS',
        ]);
    }

    // ====================== MÉTODO PARA LOGOUT ======================
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
    // ====================== MÉTODO PARA REDIRECIONAMENTO APÓS LOGIN ======================
    protected function redirectTo()
    {
        return '/dashboard';
    }
}