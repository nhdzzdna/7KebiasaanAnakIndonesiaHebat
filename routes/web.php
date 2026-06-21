<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\MonitoringController as GuruMonitoringController;

use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\KegiatanController;
use App\Http\Controllers\Siswa\StudentProfileController;

use App\Http\Controllers\ProfileController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth', 'role:admin']);

Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/admin/profile', function () {
    return Inertia::render('Admin/Profile/Index');
});

Route::get('/guru/dashboard', function () {
    return Inertia::render('Guru/Dashboard');
})->middleware(['auth', 'role:guru']);

Route::get('/siswa/dashboard', function () {
    return Inertia::render('Siswa/Dashboard');
})->middleware(['auth', 'role:siswa']);
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return Inertia::render('Welcome', [

        'canLogin' =>
            Route::has('login'),

        'canRegister' =>
            false,

        'laravelVersion' =>
            Application::VERSION,

        'phpVersion' =>
            PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| DEFAULT DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    return Inertia::render('Dashboard');

})->middleware([
    'auth',
    'verified'
])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'check.account.status'
])->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'check.account.status',
    'role:admin'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/dashboard',
        [AdminDashboardController::class, 'index']
    );

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/users',
        [UserController::class, 'index']
    );

    Route::post(
        '/admin/users',
        [UserController::class, 'store']
    );

    Route::put(
        '/admin/users/{user}',
        [UserController::class, 'update']
    );

    Route::delete(
        '/admin/users/{user}',
        [UserController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | CLASS MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/classes',
        [SchoolClassController::class, 'index']
    );

    Route::post(
        '/admin/classes',
        [SchoolClassController::class, 'store']
    );

    Route::put(
        '/admin/classes/{schoolClass}',
        [SchoolClassController::class, 'update']
    );

    Route::delete(
        '/admin/classes/{schoolClass}',
        [SchoolClassController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | GLOBAL MONITORING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/monitoring',
        [AdminMonitoringController::class, 'index']
    );

    Route::get(
        '/admin/monitoring/{kegiatan}',
        [AdminMonitoringController::class, 'show']
    );
});

/*
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'check.account.status',
    'role:guru'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/guru/dashboard',
        [GuruDashboardController::class, 'index']
    );

    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/guru/monitoring',
        [GuruMonitoringController::class, 'index']
    );

    Route::get(
        '/guru/monitoring/{kegiatan}',
        [GuruMonitoringController::class, 'show']
    );

    Route::patch(
        '/guru/monitoring/{kegiatan}/evaluasi',
        [GuruMonitoringController::class, 'evaluasi']
    );

    Route::get('/guru/monitoring/show', function () {
        return Inertia::render('Guru/Monitoring/Show');
    });

    Route::get('/guru/rekap', function () {
        return Inertia::render('Guru/Rekap/Index');
    });

    Route::get('/guru/profile', function () {
        return Inertia::render('Guru/Profile/Index');
    });
});

/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'check.account.status',
    'role:siswa'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/siswa/dashboard',
        [SiswaDashboardController::class, 'index']
    );

    /*
    |--------------------------------------------------------------------------
    | KEGIATAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/siswa/kegiatan',
        [KegiatanController::class, 'index']
    );

    Route::post(
        '/siswa/kegiatan',
        [KegiatanController::class, 'store']
    );

    Route::get(
        '/siswa/kegiatan/selfie',
        [KegiatanController::class, 'selfie']
    );
    Route::get(
        '/siswa/kegiatan/succes',
        [KegiatanController::class, 'success']
    );

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/siswa/riwayat',
        [KegiatanController::class, 'history']
    );

    Route::get(
        '/siswa/riwayat/show', function () {
        return Inertia::render('Siswa/Riwayat/Show');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/siswa/profile',
        [StudentProfileController::class, 'index']
    );

    Route::patch(
        '/siswa/profile',
        [StudentProfileController::class, 'update']
    );
});

require __DIR__.'/auth.php';
