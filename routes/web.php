<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\MonitoringController as GuruMonitoringController;
use App\Http\Controllers\Guru\RekapController;

use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\KegiatanController;
use App\Http\Controllers\Siswa\StudentProfileController;

use App\Http\Controllers\ProfileController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

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
    ])->prefix('admin')->name('admin.')->group(function () {

    Route::get(
            '/profile',
            [\App\Http\Controllers\Admin\ProfileController::class, 'index']
        )->name('profile.index');

        Route::patch(
            '/profile',
            [\App\Http\Controllers\Admin\ProfileController::class, 'update']
        )->name('profile.update');

        Route::patch(
            '/profile/password',
            [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword']
        )->name('profile.password');

        Route::post(
            '/profile/foto',
            [\App\Http\Controllers\Admin\ProfileController::class, 'uploadFoto']
        )->name('profile.foto');

        Route::patch(
            '/profile/settings',
            [\App\Http\Controllers\Admin\ProfileController::class, 'updateSettings']
        )->name('profile.settings');

        Route::delete(
            '/profile/reset-kegiatan',
            [\App\Http\Controllers\Admin\ProfileController::class, 'resetKegiatanData']
        )->name('profile.reset-kegiatan');

        Route::patch(
            '/profile/deactivate-students',
            [\App\Http\Controllers\Admin\ProfileController::class, 'deactivateAllStudents']
        )->name('profile.deactivate-students');

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | USER MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users',
            [UserController::class, 'index']
        )->name('users.index');

        Route::post(
            '/users',
            [UserController::class, 'store']
        )->name('users.store');

        Route::get(
            '/users/corrections',
            [UserController::class, 'corrections']
        )->name('users.corrections');

        Route::patch(
            '/users/corrections/{correction}/resolve',
            [UserController::class, 'resolveCorrection']
        )->name('users.corrections.resolve');

        Route::put(
            '/users/{user}',
            [UserController::class, 'update']
        )->name('users.update');

        Route::patch(
            '/users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset-password');

        Route::delete(
            '/users/{user}',
            [UserController::class, 'destroy']
        )->name('users.destroy');

    /*
    |--------------------------------------------------------------------------
    | CLASS MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/classes',
        [SchoolClassController::class, 'index']
    )->name('classes.index');

    Route::post(
        '/classes',
        [SchoolClassController::class, 'store']
    )->name('classes.store');

    Route::put(
        '/classes/{schoolClass}',
        [SchoolClassController::class, 'update']
    )->name('classes.update');

    Route::delete(
        '/classes/{schoolClass}',
        [SchoolClassController::class, 'destroy']
    )->name('classes.destroy');

    /*
    |--------------------------------------------------------------------------
    | GLOBAL MONITORING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/monitoring',
        [AdminMonitoringController::class, 'index']
    )->name('monitoring.index');

    Route::get(
        '/monitoring/{kegiatan}',
        [AdminMonitoringController::class, 'show']
    )->name('monitoring.show');
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
])->prefix('guru')->name('guru.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [GuruDashboardController::class, 'index']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/monitoring',
        [GuruMonitoringController::class, 'index']
    )->name('monitoring.index');

    Route::get(
        '/monitoring/{kegiatan}',
        [GuruMonitoringController::class, 'show']
    )->name('monitoring.show');

    Route::patch(
        '/monitoring/{kegiatan}/evaluasi',
        [GuruMonitoringController::class, 'evaluasi']
    )->name('monitoring.evaluasi');

    /*
    |--------------------------------------------------------------------------
    | REKAP
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/rekap',
        [RekapController::class, 'index']
    )->name('rekap.index');

    Route::get(
        '/rekap/pdf',
        [RekapController::class, 'exportPdf']
    )->name('rekap.pdf');

    Route::get(
        '/rekap/excel',
        [RekapController::class, 'exportExcel']
    )->name('rekap.excel');

    Route::get(
        '/rekap/grafik',
        [RekapController::class, 'grafik']
    )->name('rekap.grafik');

    Route::get(
            '/profile',
            [\App\Http\Controllers\Guru\ProfileController::class, 'index']
        )->name('profile.index');

        Route::patch(
            '/profile',
            [\App\Http\Controllers\Guru\ProfileController::class, 'update']
        )->name('profile.update');

        Route::patch(
            '/profile/password',
            [\App\Http\Controllers\Guru\ProfileController::class, 'updatePassword']
        )->name('profile.password');

        Route::post(
            '/profile/foto',
            [\App\Http\Controllers\Guru\ProfileController::class, 'uploadFoto']
        )->name('profile.foto');

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
])->prefix('siswa')->name('siswa.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [SiswaDashboardController::class, 'index']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | KEGIATAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/kegiatan',
        [KegiatanController::class, 'index']
    )->name('kegiatan.index');

    Route::get(
        '/kegiatan/selfie',
        [KegiatanController::class, 'selfie']
    )->name('kegiatan.selfie');

    Route::get(
        '/kegiatan/success',
        [KegiatanController::class, 'success']
    )->name('kegiatan.success');

    Route::post(
        '/kegiatan',
        [KegiatanController::class, 'store']
    )->name('kegiatan.store');

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
        '/riwayat',
        [KegiatanController::class, 'history']
    )->name('riwayat.index');

    Route::get(
        '/riwayat/{kegiatan}',
        [KegiatanController::class, 'show']
    )->name('riwayat.show');

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
        '/profile',
        [StudentProfileController::class, 'index']
    )->name('profile.index');

    Route::post(
            '/profile/foto',
            [StudentProfileController::class, 'uploadFoto']
        )->name('profile.foto');

    Route::patch(
        '/profile',
        [StudentProfileController::class, 'update']
    )->name('profile.update');

Route::post(
        '/profile/report-correction',
        [StudentProfileController::class, 'reportCorrection']
    )->name('profile.report-correction');
});

require __DIR__ . '/auth.php';
