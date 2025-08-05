<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
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
    
    // DASHBOARD PRINCIPAL
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    
    // ====================== ROTAS API ======================
    Route::prefix("api")->middleware(["auth"])->group(function () {
        
        // ROTA DE TESTE
        Route::get("/teste", function () {
            return response()->json(["message" => "Rota de teste API funcionando!"]);
        });

        // DISCIPLINAS PARA INDEPENDENTES (ACESSO INDIVIDUAL) E ALUNOS
        Route::get("/disciplinas", [DisciplinaController::class, "index"]);
        Route::post("/disciplinas", [DisciplinaController::class, "store"]);
        Route::get("/disciplinas/{id}", [DisciplinaController::class, "show"]);
        Route::put("/disciplinas/{id}", [DisciplinaController::class, "update"]);
        Route::delete("/disciplinas/{id}", [DisciplinaController::class, "destroy"]);
        
        // TÓPICOS PARA INDEPENDENTES (ACESSO INDIVIDUAL) E ALUNOS
        Route::get("/disciplinas/{disciplinaId}/topicos", [TopicoController::class, "index"]);
        Route::post("/topicos", [TopicoController::class, "store"]);
        Route::get("/topicos/{id}", [TopicoController::class, "show"]);
        Route::put("/topicos/{id}", [TopicoController::class, "update"]);
        Route::delete("/topicos/{id}", [TopicoController::class, "destroy"]);
        
        // FLASHCARDS 
        Route::get("/topicos/{topicoId}/flashcards", [FlashcardController::class, "index"]);
        Route::post("/flashcards", [FlashcardController::class, "store"]);
        Route::get("/flashcards/{id}", [FlashcardController::class, "show"]);
        Route::put("/flashcards/{id}", [FlashcardController::class, "update"]);
        Route::delete("/flashcards/{id}", [FlashcardController::class, "destroy"]);
        
        // PERGUNTAS 
        Route::get("/flashcards/{flashcardId}/perguntas", [PerguntaFlashcardController::class, "index"]);
        Route::post("/perguntas", [PerguntaFlashcardController::class, "store"]);
        Route::get("/perguntas/{id}", [PerguntaFlashcardController::class, "show"]);
        Route::put("/perguntas/{id}", [PerguntaFlashcardController::class, "update"]);
        Route::delete("/perguntas/{id}", [PerguntaFlashcardController::class, "destroy"]);
    });
});
