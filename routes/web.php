<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Welcome / Landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Rotas que precisam de autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    // Route::get('/dashboard', [DashboardController::class, 'index'])
    //     ->name('dashboard');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de administração (somente para Administrador)
    // Route::middleware(['role:Administrador'])->prefix('admin')->group(function () {
    //     // Gerir usuários
    //     Route::resource('users', UserController::class);

    //     // Gerir medicamentos
    //     Route::resource('medicamentos', MedicamentoController::class);
    // });

    // Rotas gerais para todos usuários autenticados (Farmacêutico, Caixa, etc.)
    // Route::middleware(['role:Farmaceutico,Caixa,Gerente,Controlador_de_stock,Assistente'])->group(function () {
    //     Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('medicamentos.index');
    //     Route::get('/vendas', [MedicamentoController::class, 'vendas'])->name('vendas.index');
    //     Route::get('/compras', [MedicamentoController::class, 'compras'])->name('compras.index');
    // });
});

// Auth routes (Breeze)
require __DIR__.'/auth.php';
