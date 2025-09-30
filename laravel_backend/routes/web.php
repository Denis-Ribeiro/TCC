<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\AlunoController;

// Redireciona a rota raiz ('/') para a página de login.
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de Autenticação
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:professor,aluno');

// Grupo de rotas para PROFESSORES
Route::middleware(['auth:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
    Route::post('/atividades', [ProfessorController::class, 'storeAtividade'])->name('atividades.store');
    Route::delete('/atividades/{atividade}', [ProfessorController::class, 'destroyAtividade'])->name('atividades.destroy');
});

// Grupo de rotas para ALUNOS
Route::middleware(['auth:aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    Route::get('/dashboard', [AlunoController::class, 'dashboard'])->name('dashboard');
    // Rota para a página de atividades
    Route::get('/atividades', [AlunoController::class, 'atividades'])->name('atividades');
    Route::post('/quiz/{idAtividade}/submit', [AlunoController::class, 'submitQuiz'])->name('quiz.submit');
});

