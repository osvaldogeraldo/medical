<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProfileController;
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
        Route::get('/{uuid}/edit', 'edit')->name('edit');
        Route::get('/{uuid}', 'show')->name('show'); // Para view mode
        Route::post('/', 'store')->name('store');
        Route::put('/{uuid}', 'update')->name('update');
        Route::delete('/{uuid}', 'destroy')->name('destroy');
        Route::post('/restore/{uuid}', 'restore')->name('restore');
        Route::post('/toggle/{uuid}', 'toggle')->name('toggle');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('/import-excel', 'importExcel')->name('importExcel');
        Route::get('/template', 'template')->name('template');
        // Rotas para remover anexos e imagens
        Route::delete('/{uuid}/attachments/{attachmentId}', 'removeAttachment')->name('removeAttachment');
        Route::delete('/{uuid}/images/{imageIndex}', 'removeImage')->name('removeImage');
    });
});

// Auth routes (Breeze)
require __DIR__.'/auth.php';
