<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaIndependente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    
    // EXIBE O DASHBOARD PRINCIPAL PARA USUÁRIOS INDEPENDENTES (ACESSO INDIVIDUAL)
    public function index()
    {
        $disciplinas = DisciplinaIndependente::where("id_usuario", Auth::id())
                                            ->with("topicos.flashcards")
                                            ->get();

        return view("dashboard.index", compact("disciplinas"));
    }
}