<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\IT\CategoryController as ITCategoryController;
use App\Http\Controllers\IT\DashboardController as ITDashboardController;
use App\Http\Controllers\IT\EmplacementController as ITEmplacementController;
use App\Http\Controllers\IT\EquipementController as ITEquipementController;
use App\Http\Controllers\IT\MouvementController as ITMouvementController;
use App\Http\Controllers\IT\ProductController as ITProductController;
use App\Http\Controllers\IT\StockController as ITStockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion des utilisateurs
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // Configuration générale & Paramètres (e-mail directeur)
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Journal d'activités & Audit
    Route::get('/activities', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activities.index');
    Route::get('/activities/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activities.show');
});

// Responsable IT routes
Route::middleware(['auth', 'role:responsable_it'])->prefix('it')->name('it.')->group(function () {
    Route::get('/dashboard', [ITDashboardController::class, 'index'])->name('dashboard');

    // Catégories de matériel
    Route::get('/categories', [ITCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [ITCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [ITCategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/toggle-status', [ITCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::delete('/categories/{category}', [ITCategoryController::class, 'destroy'])->name('categories.destroy');

    // Catalogue du matériel (Produits)
    Route::resource('products', ITProductController::class);
    Route::patch('/products/{product}/toggle-status', [ITProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Équipements informatiques (Unités physiques sérialisées)
    Route::resource('equipements', ITEquipementController::class);
    Route::patch('/equipements/{equipement}/toggle-status', [ITEquipementController::class, 'toggleStatus'])->name('equipements.toggle-status');

    // Emplacements physiques de stockage
    Route::resource('emplacements', ITEmplacementController::class)->except(['create', 'edit']);
    Route::patch('/emplacements/{emplacement}/toggle-status', [ITEmplacementController::class, 'toggleStatus'])->name('emplacements.toggle-status');

    // Gestion des Stocks & Mouvements
    Route::get('/stocks', [ITStockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/entree', [ITStockController::class, 'createEntree'])->name('stocks.entree');
    Route::post('/stocks/entree', [ITStockController::class, 'storeEntree'])->name('stocks.entree.store');
    Route::get('/stocks/sortie', [ITStockController::class, 'createSortie'])->name('stocks.sortie');
    Route::post('/stocks/sortie', [ITStockController::class, 'storeSortie'])->name('stocks.sortie.store');
    Route::get('/stocks/transfert', [ITStockController::class, 'createTransfert'])->name('stocks.transfert');
    Route::post('/stocks/transfert', [ITStockController::class, 'storeTransfert'])->name('stocks.transfert.store');
    Route::get('/stocks/correction', [ITStockController::class, 'createCorrection'])->name('stocks.correction');
    Route::post('/stocks/correction', [ITStockController::class, 'storeCorrection'])->name('stocks.correction.store');

    // Historique des mouvements
    Route::get('/mouvements', [ITMouvementController::class, 'index'])->name('mouvements.index');
    Route::get('/mouvements/{mouvement}', [ITMouvementController::class, 'show'])->name('mouvements.show');

    // Demandes de matériel par Gmail
    Route::resource('demandes', \App\Http\Controllers\IT\DemandeMaterielController::class)->except(['destroy']);
    Route::post('/demandes/{demande}/gmail-opened', [\App\Http\Controllers\IT\DemandeMaterielController::class, 'recordGmailOpened'])->name('demandes.gmail-opened');
    Route::patch('/demandes/{demande}/mark-sent', [\App\Http\Controllers\IT\DemandeMaterielController::class, 'markSent'])->name('demandes.mark-sent');
    Route::post('/demandes/{demande}/duplicate', [\App\Http\Controllers\IT\DemandeMaterielController::class, 'duplicate'])->name('demandes.duplicate');
    Route::patch('/demandes/{demande}/archive', [\App\Http\Controllers\IT\DemandeMaterielController::class, 'archive'])->name('demandes.archive');

    // Rapports & Exports (PDF / Excel / Gmail)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\IT\ReportController::class, 'index'])->name('index');
        Route::get('/inventaire', [\App\Http\Controllers\IT\ReportController::class, 'inventaire'])->name('inventaire');
        Route::get('/stocks', fn() => redirect()->route('it.reports.inventaire'))->name('stocks');
        Route::get('/mouvements', [\App\Http\Controllers\IT\ReportController::class, 'mouvements'])->name('mouvements');
        Route::get('/demandes', fn() => redirect()->route('it.demandes.index'))->name('demandes');
        Route::get('/emplacements', fn() => redirect()->route('it.emplacements.index'))->name('emplacements');
    });
});

// Shared authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'responsable_it' => redirect()->route('it.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
