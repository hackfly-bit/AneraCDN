<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public file download (for public files)
Route::get('/files/{slug}/download', [FileController::class, 'download'])->name('files.download');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
    
    // File management routes
    Route::apiResource('files', FileController::class);
    Route::get('/files/stats', [FileController::class, 'stats']);
    
    // File upload route for dashboard
    Route::post('/files/upload', [FileController::class, 'upload'])
        ->name('api.files.upload')
        ->middleware('throttle:uploads');
    
    // Rate limiting for file uploads
    Route::post('/files', [FileController::class, 'store'])
        ->middleware('throttle:uploads');
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});