<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Independente;

// ====================== REGISTERCONTROLLER ======================
// RESPONSÁVEL POR TODAS AS OPERAÇÕES RELACIONADAS AO CADASTRO
class RegisterController extends Controller
{
    // ====================== MÉTODO PARA EXIBIR A VIEW DE CADASTRO ======================
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // ====================== MÉTODO PARA PROCESSAR O CADASTRO ======================
    public function register(Request $request)
    {
        // ====================== VALIDAÇÃO DOS DADOS DO FORMULÁRIO ======================
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|confirmed|min:8',
            'user_type' => 'required|in:independente,escola',
        ]);

        // ====================== CRIAÇÃO DO USUÁRIO PRINCIPAL ======================
        $usuario = Usuario::create([
            'nome' => $data['name'],
            'email' => $data['email'],
            'senha' => bcrypt($data['password']),
            'tipo' => 'independente', // DEFININDO TIPO FIXO COMO INDEPENDENTE
            'data_cadastro' => now(),
        ]);

        // ====================== CRIAÇÃO DO PERFIL INDEPENDENTE ======================
        Independente::create([
            'id_usuario' => $usuario->id_usuario,
        ]);

        // ====================== AUTENTICAÇÃO AUTOMÁTICA APÓS CADASTRO ======================
        Auth::login($usuario);

        // ====================== REDIRECIONAMENTO PARA DASHBOARD ======================
        return redirect('/dashboard');
    }
}