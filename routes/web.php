<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
    Route::post('/students/sync', [App\Http\Controllers\StudentController::class, 'sync'])->name('students.sync');
    Route::get('/students/export', [App\Http\Controllers\StudentController::class, 'export'])->name('students.export');
    
    Route::get('/summary', [App\Http\Controllers\SummaryController::class, 'index'])->name('summary.index');
    Route::post('/summary/sync', [App\Http\Controllers\SummaryController::class, 'sync'])->name('summary.sync');
});

require __DIR__.'/auth.php';
