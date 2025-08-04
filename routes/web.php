<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplinaIndependenteController;
use App\Http\Controllers\TopicoIndependenteController;
use App\Http\Controllers\FlashcardIndependenteController;
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

        // DISCIPLINAS
        Route::get("/disciplinas", [DisciplinaIndependenteController::class, "index"]);
        Route::post("/disciplinas", [DisciplinaIndependenteController::class, "store"]);
        Route::get("/disciplinas/{id}", [DisciplinaIndependenteController::class, "show"]);
        Route::put("/disciplinas/{id}", [DisciplinaIndependenteController::class, "update"]);
        Route::delete("/disciplinas/{id}", [DisciplinaIndependenteController::class, "destroy"]);
        
        // TÓPICOS
        Route::get("/disciplinas/{disciplinaId}/topicos", [TopicoIndependenteController::class, "index"]);
        Route::post("/topicos", [TopicoIndependenteController::class, "store"]);
        Route::get("/topicos/{id}", [TopicoIndependenteController::class, "show"]);
        Route::put("/topicos/{id}", [TopicoIndependenteController::class, "update"]);
        Route::delete("/topicos/{id}", [TopicoIndependenteController::class, "destroy"]);
        
        // FLASHCARDS
        Route::get("/topicos/{topicoId}/flashcards", [FlashcardIndependenteController::class, "index"]);
        Route::post("/flashcards", [FlashcardIndependenteController::class, "store"]);
        Route::get("/flashcards/{id}", [FlashcardIndependenteController::class, "show"]);
        Route::put("/flashcards/{id}", [FlashcardIndependenteController::class, "update"]);
        Route::delete("/flashcards/{id}", [FlashcardIndependenteController::class, "destroy"]);
        
        // PERGUNTAS
        Route::get("/flashcards/{flashcardId}/perguntas", [PerguntaFlashcardController::class, "index"]);
        Route::post("/perguntas", [PerguntaFlashcardController::class, "store"]);
        Route::get("/perguntas/{id}", [PerguntaFlashcardController::class, "show"]);
        Route::put("/perguntas/{id}", [PerguntaFlashcardController::class, "update"]);
        Route::delete("/perguntas/{id}", [PerguntaFlashcardController::class, "destroy"]);
    });
});
