<?php

use App\Http\Controllers\Siswa\KegiatanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth', 'role:admin']);

Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/guru/dashboard', function () {
    return Inertia::render('Guru/Dashboard');
})->middleware(['auth', 'role:guru']);

Route::get('/siswa/dashboard', function () {
    return Inertia::render('Siswa/Dashboard');
})->middleware(['auth', 'role:siswa']);

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
});

Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware(['auth', 'role:admin']);

Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'role:admin']);

Route::get('/siswa/kegiatan', [KegiatanController::class, 'index'])
    ->middleware(['auth', 'role:siswa']);

Route::post('/siswa/kegiatan', [KegiatanController::class, 'store'])
    ->middleware(['auth', 'role:siswa']);

require __DIR__.'/auth.php';
