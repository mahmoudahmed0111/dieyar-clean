<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChaletController;
use App\Http\Controllers\API\DamageController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\MaintenanceController;
use App\Http\Controllers\API\PestControlController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\DeepCleaningController;
use App\Http\Controllers\API\RegularCleaningController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CleaningController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes للضيوف (بدون مصادقة)
Route::post('/login', [AuthController::class, 'login']);

// Chalets routes للضيوف (بدون مصادقة)
Route::prefix('chalets')->group(function () {
    Route::get('/', [ChaletController::class, 'index']);
    Route::get('/stats', [ChaletController::class, 'stats']);
    Route::get('/info', [ChaletController::class, 'chaletInfo']);
    Route::get('/service-info', [ChaletController::class, 'serviceInfo']);
    Route::get('/damage-info', [ChaletController::class, 'damageInfo']);
    Route::get('/{chalet}', [ChaletController::class, 'show']);
});

// Routes محمية بالمصادقة
Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'updatePassword']);


    // Chalets routes
    Route::prefix('chalets')->group(function () {
        Route::get('/', [ChaletController::class, 'index']);
        Route::get('/{chalet}', [ChaletController::class, 'show']);
    });

    // Regular Cleanings routes
    Route::prefix('regular-cleanings')->group(function () {
        Route::get('/', [RegularCleaningController::class, 'index']);
        Route::post('/', [RegularCleaningController::class, 'store']);
        Route::get('/{regularCleaning}', [RegularCleaningController::class, 'show']);
        Route::put('/{regularCleaning}', [RegularCleaningController::class, 'update']);
        Route::post('/{regularCleaning}/images', [RegularCleaningController::class, 'uploadImages']);
        Route::post('/{regularCleaning}/videos', [RegularCleaningController::class, 'uploadVideos']);
    });

    // Deep Cleanings routes
    Route::prefix('deep-cleanings')->group(function () {
        Route::get('/', [DeepCleaningController::class, 'index']);
        Route::post('/', [DeepCleaningController::class, 'store']);
        Route::get('/{deepCleaning}', [DeepCleaningController::class, 'show']);
        Route::put('/{deepCleaning}', [DeepCleaningController::class, 'update']);
        Route::post('/{deepCleaning}/images', [DeepCleaningController::class, 'uploadImages']);
        Route::post('/{deepCleaning}/videos', [DeepCleaningController::class, 'uploadVideos']);
    });

    // Service routes - تقارير الخدمات (صيانة ومكافحة)
    Route::prefix('services')->group(function () {
        Route::post('/upload', [ServiceController::class, 'uploadService']);
    });

    // Damages routes - تقارير الأضرار
    Route::prefix('damages')->group(function () {
        Route::post('/upload', [DamageController::class, 'uploadDamage']);

    });

    // Inventory routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        Route::get('/{inventory}', [InventoryController::class, 'show']);
    });

    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/stats', [NotificationController::class, 'stats']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'destroyAll']);
        Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken']);
        Route::post('/test', [NotificationController::class, 'sendTestNotification']);
    });

    // Cleaning routes - دالة رفع النظافة الشاملة
    Route::prefix('cleaning')->group(function () {
        Route::post('/upload', [CleaningController::class, 'uploadCleaning']);
        Route::get('/history', [CleaningController::class, 'getCleaningHistory']);
        Route::get('/details/{id}', [CleaningController::class, 'getCleaningDetails']);
    });
});
