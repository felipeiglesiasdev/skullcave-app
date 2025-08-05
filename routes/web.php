<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\DashboardIndependenteController;
use App\Http\Controllers\Dashboard\DashboardAlunoController;
use App\Http\Controllers\Dashboard\DashboardProfessorController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\TopicoController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\PerguntaFlashcardController;

// ====================== ROTAS PÚBLICAS ======================
Route::get("/", function () {
    return view("welcome");
});

// ====================== ROTAS DE AUTENTICAÇÃO ======================
Route::controller(LoginController::class)->group(function () {
    Route::get("/login", "showLoginForm")->name("login");
    Route::post("/login", "login");
    Route::post("/logout", "logout")->name("logout");
});

Route::controller(RegisterController::class)->group(function () {
    Route::get("/register", "showRegistrationForm")->name("register");
    Route::post("/register", "register");
});

// ====================== ROTAS PROTEGIDAS ======================
Route::middleware("auth")->group(function () {
    
    // DASHBOARD PRINCIPAL (REDIRECIONAMENTO AUTOMÁTICO)
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    
    // ====================== DASHBOARDS ESPECÍFICOS ======================
    
    // Dashboard Independente
    Route::get("/dashboard/independente", [DashboardIndependenteController::class, "index"])->name("dashboard.independente");
    
    // Dashboard Aluno
    Route::get("/dashboard/aluno", [DashboardAlunoController::class, "index"])->name("dashboard.aluno");
    
    // Dashboard Professor
    Route::get("/dashboard/professor", [DashboardProfessorController::class, "index"])->name("dashboard.professor");
    
    // ====================== ROTAS API ESPECÍFICAS POR TIPO DE USUÁRIO ======================
    
    // ===== APIs PARA INDEPENDENTES =====
    Route::prefix("dashboard/api/independente")->middleware(["auth"])->group(function () {
        
        // Disciplinas
        Route::get("/disciplinas", [DashboardIndependenteController::class, "getDisciplinas"]);
        Route::post("/disciplinas", [DashboardIndependenteController::class, "criarDisciplina"]);
        Route::put("/disciplinas/{id}", [DashboardIndependenteController::class, "editarDisciplina"]);
        Route::delete("/disciplinas/{id}", [DashboardIndependenteController::class, "excluirDisciplina"]);
        
        // Tópicos
        Route::get("/disciplinas/{disciplinaId}/topicos", [DashboardIndependenteController::class, "getTopicos"]);
        Route::post("/topicos", [DashboardIndependenteController::class, "criarTopico"]);
        Route::put("/topicos/{id}", [DashboardIndependenteController::class, "editarTopico"]);
        Route::delete("/topicos/{id}", [DashboardIndependenteController::class, "excluirTopico"]);
        
        // Flashcards
        Route::get("/topicos/{topicoId}/flashcards", [DashboardIndependenteController::class, "getFlashcards"]);
        Route::post("/flashcards", [DashboardIndependenteController::class, "criarFlashcard"]);
        Route::put("/flashcards/{id}", [DashboardIndependenteController::class, "editarFlashcard"]);
        Route::delete("/flashcards/{id}", [DashboardIndependenteController::class, "excluirFlashcard"]);
        
        // Estatísticas
        Route::get("/estatisticas", [DashboardIndependenteController::class, "getEstatisticas"]);
    });
    
    // ===== APIs PARA ALUNOS =====
    Route::prefix("api/aluno")->middleware(["auth"])->group(function () {
        
        // Disciplinas próprias
        Route::get("/minhas-disciplinas", [DashboardAlunoController::class, "getMinhasDisciplinas"]);
        Route::post("/disciplinas", [DashboardAlunoController::class, "criarDisciplina"]);
        Route::put("/disciplinas/{id}", [DashboardAlunoController::class, "editarDisciplina"]);
        Route::delete("/disciplinas/{id}", [DashboardAlunoController::class, "excluirDisciplina"]);
        
        // Disciplinas do professor
        Route::get("/disciplinas-professor", [DashboardAlunoController::class, "getDisciplinasProfessor"]);
        
        // Tópicos (próprios e do professor)
        Route::get("/disciplinas/{disciplinaId}/topicos/{tipo}", [DashboardAlunoController::class, "getTopicos"]);
        Route::post("/topicos", [DashboardAlunoController::class, "criarTopico"]);
        Route::put("/topicos/{id}", [DashboardAlunoController::class, "editarTopico"]);
        Route::delete("/topicos/{id}", [DashboardAlunoController::class, "excluirTopico"]);
        
        // Flashcards (próprios e do professor)
        Route::get("/topicos/{topicoId}/flashcards/{tipo}", [DashboardAlunoController::class, "getFlashcards"]);
        Route::post("/flashcards", [DashboardAlunoController::class, "criarFlashcard"]);
        Route::put("/flashcards/{id}", [DashboardAlunoController::class, "editarFlashcard"]);
        Route::delete("/flashcards/{id}", [DashboardAlunoController::class, "excluirFlashcard"]);
        
        // Estatísticas
        Route::get("/estatisticas", [DashboardAlunoController::class, "getEstatisticas"]);
    });
    
    // ===== APIs PARA PROFESSORES =====
    Route::prefix("api/professor")->middleware(["auth"])->group(function () {
        
        // Turmas
        Route::get("/turmas", [DashboardProfessorController::class, "getTurmas"]);
        Route::get("/turmas/{turmaId}/disciplinas", [DashboardProfessorController::class, "getDisciplinasTurma"]);
        Route::get("/turmas/{turmaId}/alunos", [DashboardProfessorController::class, "getAlunosTurma"]);
        
        // Disciplinas
        Route::post("/disciplinas", [DashboardProfessorController::class, "criarDisciplina"]);
        Route::put("/disciplinas/{id}", [DashboardProfessorController::class, "editarDisciplina"]);
        Route::delete("/disciplinas/{id}", [DashboardProfessorController::class, "excluirDisciplina"]);
        
        // Tópicos
        Route::get("/disciplinas/{disciplinaId}/topicos", [DashboardProfessorController::class, "getTopicos"]);
        Route::post("/topicos", [DashboardProfessorController::class, "criarTopico"]);
        Route::put("/topicos/{id}", [DashboardProfessorController::class, "editarTopico"]);
        Route::delete("/topicos/{id}", [DashboardProfessorController::class, "excluirTopico"]);
        
        // Flashcards
        Route::get("/topicos/{topicoId}/flashcards", [DashboardProfessorController::class, "getFlashcards"]);
        Route::post("/flashcards", [DashboardProfessorController::class, "criarFlashcard"]);
        Route::put("/flashcards/{id}", [DashboardProfessorController::class, "editarFlashcard"]);
        Route::delete("/flashcards/{id}", [DashboardProfessorController::class, "excluirFlashcard"]);
        
        // Atribuição de flashcards
        Route::post("/atribuir-flashcards", [DashboardProfessorController::class, "atribuirFlashcards"]);
        
        // Estatísticas
        Route::get("/estatisticas", [DashboardProfessorController::class, "getEstatisticas"]);
    });
    
    
});
