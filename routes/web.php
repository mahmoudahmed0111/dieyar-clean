<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\CleanerController;
use App\Http\Controllers\Dashboard\SettingController;

// تطبيق middleware للغة على جميع routes
Route::middleware('setlocale')->group(function () {

    // Route لتغيير اللغة
    Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

    // Routes for guests (not authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Routes for authenticated users
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // User Management Routes
        Route::prefix('dashboard/users')->name('dashboard.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Cleaners Management Routes
        Route::prefix('dashboard/cleaners')->name('dashboard.cleaners.')->group(function () {
            Route::get('/', [CleanerController::class, 'index'])->name('index');
            Route::get('/create', [CleanerController::class, 'create'])->name('create');
            Route::post('/', [CleanerController::class, 'store'])->name('store');
            Route::get('/{cleaner}/edit', [CleanerController::class, 'edit'])->name('edit');
            Route::put('/{cleaner}', [CleanerController::class, 'update'])->name('update');
            Route::delete('/{cleaner}', [CleanerController::class, 'destroy'])->name('destroy');
            Route::patch('/{cleaner}/toggle-status', [CleanerController::class, 'toggleStatus'])->name('toggle-status');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('chalets', App\Http\Controllers\Dashboard\ChaletController::class);
            Route::post('chalets/upload-video', [App\Http\Controllers\Dashboard\ChaletController::class, 'uploadVideo'])->name('chalets.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('damages', App\Http\Controllers\Dashboard\DamageController::class);
            Route::post('damages/upload-video', [App\Http\Controllers\Dashboard\DamageController::class, 'uploadVideo'])->name('damages.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('deep_cleanings', App\Http\Controllers\Dashboard\DeepCleaningController::class);
            Route::post('deep_cleanings/upload-video', [App\Http\Controllers\Dashboard\DeepCleaningController::class, 'uploadVideo'])->name('deep_cleanings.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('regular_cleanings', App\Http\Controllers\Dashboard\RegularCleaningController::class);
            Route::post('regular_cleanings/upload-video', [App\Http\Controllers\Dashboard\RegularCleaningController::class, 'uploadVideo'])->name('regular_cleanings.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('maintenance', App\Http\Controllers\Dashboard\MaintenanceController::class);
            Route::post('maintenance/upload-video', [App\Http\Controllers\Dashboard\MaintenanceController::class, 'uploadVideo'])->name('maintenance.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('pest_controls', App\Http\Controllers\Dashboard\PestControlController::class);
            Route::post('pest_controls/upload-video', [App\Http\Controllers\Dashboard\PestControlController::class, 'uploadVideo'])->name('pest_controls.uploadVideo');
        });

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('inventory', App\Http\Controllers\Dashboard\InventoryController::class);
        });

        //settings
        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
            Route::resource('settings', SettingController::class);
        });

        // صفحة مثال رفع النظافة
        Route::get('/cleaning-upload-example', function () {
            return view('cleaning-upload-example');
        })->name('cleaning.upload.example');
    });

});
