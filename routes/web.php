<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\DashboardController;

// Welcome / Landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categorias
    Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'index')->name('search');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{uuid}/edit', 'edit')->name('edit');
        Route::put('/{uuid}', 'update')->name('update');
        Route::delete('/{uuid}', 'destroy')->name('destroy');
        Route::post('/restore/{uuid}', 'restore')->name('restore');
        Route::post('/toggle/{uuid}', 'toggle')->name('toggle');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
        Route::post('/import', 'importExcel')->name('import');
        Route::get('/template', 'template')->name('template');
    });

    Route::prefix('medicines')->as('medicines.')->controller(MedicineController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/{uuid}', 'details')->name('details');
        Route::post('/', 'store')->name('store');
        Route::put('/{uuid}', 'update')->name('update');
        Route::delete('/{uuid}', 'destroy')->name('destroy');
        Route::post('/restore/{uuid}', 'restore')->name('restore');
        Route::post('/toggle/{uuid}', 'toggle')->name('toggle');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('/import-excel', 'importExcel')->name('importExcel');
        Route::get('/template', 'template')->name('template');
    });
});

// Auth routes (Breeze)
require __DIR__ . '/auth.php';
