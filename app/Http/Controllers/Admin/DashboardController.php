<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Kegiatan;
use App\Models\SchoolClass;
use App\Models\User;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // TOTAL USER
        $totalUsers = User::count();

        $totalGuru = User::where(
            'role',
            'guru'
        )->count();

        $totalSiswa = User::where(
            'role',
            'siswa'
        )->count();

        // TOTAL KELAS
        $totalClasses = SchoolClass::count();

        // AKUN AKTIF
        $activeUsers = User::where(
            'status_akun',
            'aktif'
        )->count();

        // AKUN NONAKTIF
        $inactiveUsers = User::where(
            'status_akun',
            'nonaktif'
        )->count();

        // TOTAL LAPORAN
        $totalReports = Kegiatan::count();

        // LAPORAN HARI INI
        $todayReports = Kegiatan::whereDate(
            'tanggal',
            now()->toDateString()
        )->count();

        // RATA-RATA COMPLIANCE
        $averageCompliance = round(

            Kegiatan::avg(
                'compliance_percentage'
            )

            ?? 0
        );

        // LAPORAN TERBARU
        $recentReports = Kegiatan::with([
            'user'
        ])

        ->latest()

        ->take(5)

        ->get();

        // TOP SISWA
        $topStudents = Kegiatan::with([
            'user'
        ])

        ->orderByDesc(
            'compliance_percentage'
        )

        ->latest()

        ->take(5)

        ->get();

        return Inertia::render(
            'Admin/Dashboard',
            [

                'stats' => [

                    'total_users' =>
                        $totalUsers,

                    'total_guru' =>
                        $totalGuru,

                    'total_siswa' =>
                        $totalSiswa,

                    'total_classes' =>
                        $totalClasses,

                    'active_users' =>
                        $activeUsers,

                    'inactive_users' =>
                        $inactiveUsers,

                    'total_reports' =>
                        $totalReports,

                    'today_reports' =>
                        $todayReports,

                    'average_compliance' =>
                        $averageCompliance,
                ],

                'recentReports' =>
                    $recentReports,

                'topStudents' =>
                    $topStudents,
            ]
        );
    }
}