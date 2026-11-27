<?php

use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemoireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilisateurController;
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
    Route::get('/catalogue/{memoire}/lecteur', [MemoireController::class, 'lecteur'])->name('catalogue.lecteur');

    // Sélection personnelle.
    Route::get('/mes-favoris', [FavoriController::class, 'index'])->name('favoris.index');
    Route::post('/catalogue/{memoire}/favori', [FavoriController::class, 'toggle'])->name('favoris.toggle');

    // Notifications internes.
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/tout-lire', [NotificationController::class, 'toutLire'])->name('notifications.toutLire');
    Route::post('/notifications/{notification}', [NotificationController::class, 'lire'])->name('notifications.lire');
});

Route::middleware(['auth', 'role:etudiant'])->group(function () {
    Route::get('/mes-memoires', [MemoireController::class, 'miennes'])->name('memoires.miennes');
    Route::get('/deposer-un-memoire', [MemoireController::class, 'creer'])->name('memoires.create');
    Route::post('/deposer-un-memoire', [MemoireController::class, 'stocker'])->name('memoires.store');
    Route::post('/mes-memoires/{memoire}/resoumettre', [MemoireController::class, 'resoumettre'])->name('memoires.resoumettre');
});

Route::middleware(['auth', 'role:admin,bibliothecaire'])->group(function () {
    Route::get('/gestion/memoires', [MemoireController::class, 'gestion'])->name('memoires.index');
});

Route::get('/catalogue/{memoire}', [MemoireController::class, 'show'])->name('catalogue.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/annees', [AnneeAcademiqueController::class, 'index'])->name('annees.index');
    Route::post('/annees', [AnneeAcademiqueController::class, 'store'])->name('annees.store');
    Route::patch('/annees/{annee}/active', [AnneeAcademiqueController::class, 'activate'])->name('annees.activate');

    // Comptes du personnel : création, activation et désactivation.
    Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('users.index');
    Route::get('/utilisateurs/nouveau', [UtilisateurController::class, 'create'])->name('users.create');
    Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->name('users.store');
    Route::patch('/utilisateurs/{utilisateur}/statut', [UtilisateurController::class, 'toggle'])->name('users.toggle');
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
