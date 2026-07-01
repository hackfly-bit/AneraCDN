<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public file routes
Route::get('/file/{slug}', [\App\Http\Controllers\FileController::class, 'show'])->name('file.show');
Route::get('/file/{slug}/view', [\App\Http\Controllers\FileController::class, 'view'])->name('file.view');
Route::get('/file/{slug}/download', [\App\Http\Controllers\FileController::class, 'download'])->name('file.download');
Route::get('/file/{slug}/thumbnail', [\App\Http\Controllers\FileController::class, 'thumbnail'])->name('file.thumbnail');
Route::get('/file/{slug}/webp', [\App\Http\Controllers\FileController::class, 'webp'])->name('file.webp');

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/files', [DashboardController::class, 'files'])->name('dashboard.files');
    Route::get('/dashboard/upload', [DashboardController::class, 'upload'])->name('dashboard.upload');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/activities', [DashboardController::class, 'activities'])->name('dashboard.activities');
    Route::get('/dashboard/api', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('dashboard.api');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API Key Management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('api-keys', \App\Http\Controllers\ApiKeyController::class)->except(['show']);
    Route::post('api-keys/{apiKey}/regenerate', [\App\Http\Controllers\ApiKeyController::class, 'regenerate'])
        ->name('api-keys.regenerate');
});

require __DIR__.'/auth.php';
