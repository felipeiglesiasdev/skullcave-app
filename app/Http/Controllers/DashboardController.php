<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Independente;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    
    /**
     * Redireciona para o dashboard específico baseado no tipo de usuário
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar se é usuário independente
        $independente = Independente::where('id_usuario', $user->id_usuario)->first();
        if ($independente) {
            return redirect()->route('dashboard.independente');
        }
        
        // Verificar se é aluno
        $aluno = Aluno::where('id_usuario', $user->id_usuario)->first();
        if ($aluno) {
            return redirect()->route('dashboard.aluno');
        }
        
        // Verificar se é professor
        $professor = Professor::where('id_usuario', $user->id_usuario)->first();
        if ($professor) {
            return redirect()->route('dashboard.professor');
        }
        
        // Se não encontrou tipo específico, usar dashboard padrão (independente)
        return $this->dashboardIndependente();
    }
    
    /**
     * Dashboard padrão para usuários sem tipo específico (compatibilidade)
     */
    private function dashboardIndependente()
    {
        $disciplinas = Disciplina::where("id_usuario", Auth::id())
                                    ->with("topicos.flashcards")
                                    ->get();

        // Calcular estatísticas
        $totalTopicos = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->count();
        });

        $totalFlashcards = $disciplinas->sum(function ($disciplina) {
            return $disciplina->topicos->sum(function ($topico) {
                return $topico->flashcards->count();
            });
        });

        return view("dashboard.index", compact("disciplinas", "totalTopicos", "totalFlashcards"));
    }
}