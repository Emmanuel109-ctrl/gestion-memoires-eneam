<?php

use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemoireController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ValidationController;
use Illuminate\Support\Facades\Route;

// Accueil public : le site ne commence jamais par la connexion.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalogue public : notices et pages de garde accessibles sans compte.
Route::get('/catalogue', [MemoireController::class, 'index'])->name('catalogue.index');
Route::get('/catalogue/{memoire}/page-de-garde', [MemoireController::class, 'couverture'])->name('catalogue.couverture');

Route::middleware('auth')->group(function () {
    // Texte intégral et téléchargement : politiques d'accès dans MemoirePolicy.
    Route::get('/catalogue/{memoire}/lire', [MemoireController::class, 'lire'])->name('catalogue.lire');
    Route::get('/catalogue/{memoire}/telecharger', [MemoireController::class, 'telecharger'])->name('catalogue.telecharger');
});

Route::get('/catalogue/{memoire}', [MemoireController::class, 'show'])->name('catalogue.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/annees', [AnneeAcademiqueController::class, 'index'])->name('annees.index');
    Route::post('/annees', [AnneeAcademiqueController::class, 'store'])->name('annees.store');
    Route::patch('/annees/{annee}/active', [AnneeAcademiqueController::class, 'activate'])->name('annees.activate');
});

Route::middleware(['auth', 'role:admin,bibliothecaire'])->group(function () {
    Route::get('/validations', [ValidationController::class, 'index'])->name('validations.index');
    Route::get('/validations/{memoire}', [ValidationController::class, 'show'])->name('validations.show');
    Route::patch('/validations/{memoire}', [ValidationController::class, 'update'])->name('validations.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
