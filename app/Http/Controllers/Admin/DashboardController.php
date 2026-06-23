<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Kegiatan;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\DataCorrection;

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

        $totalDraft = Kegiatan::where(
            'status',
            'draft'
        )->count();

        $totalSubmitted = Kegiatan::where(
            'status',
            'submitted'
        )->count();

        $totalEvaluated = Kegiatan::where(
            'status',
            'evaluated'
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

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->latest()

        ->take(5)

        ->get();

        // TOP SISWA BERPRESTASI
        $topStudents = User::with([
            'kegiatans'
        ])

        ->where('role', 'siswa')

        ->get()

        ->map(function ($student) {

            $averageCompliance = round(

            $student->kegiatans()

                ->whereIn('status', [
                    'submitted',
                    'evaluated'
                ])

                ->avg('compliance_percentage')

                    ?? 0
            );

            return [

                'id' => $student->id,

                'name' => $student->name,

                'average_compliance' =>
                    $averageCompliance,
            ];
        })

        ->sortByDesc(
            'average_compliance'
        )

        ->take(5)

        ->values();

        // GRAFIK AKTIVITAS 7 HARI TERAKHIR
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $activityData = Kegiatan::whereBetween('tanggal', [
            $sevenDaysAgo->toDateString(),
            now()->toDateString()
        ])

        ->get()

        ->groupBy(fn ($k) => $k->tanggal->format('Y-m-d'));

        $activityChart = collect(range(0, 6))
            ->map(function ($i) use ($sevenDaysAgo, $activityData) {

                $date = $sevenDaysAgo->copy()->addDays($i);

                $dateKey = $date->format('Y-m-d');

                $items = $activityData->get($dateKey);

                return [
                    'tanggal' => $dateKey,

                    'hari' => $date->translatedFormat('D'),

                    'total_aktivitas' =>
                        $items?->count()
                        ?? 0,
                ];
            })

            ->values();

        // STATUS SISTEM (PERSENTASE PER ROLE)
        $guruAktif = User::where(
            'role',
            'guru'
        )

        ->where(
            'status_akun',
            'aktif'
        )

        ->count();

        $persenGuruAktif = $totalGuru > 0

            ? round(
                ($guruAktif / $totalGuru) * 100
            )

            : 0;

        $siswaAktif = User::where(
            'role',
            'siswa'
        )

        ->where(
            'status_akun',
            'aktif'
        )

        ->count();

        $persenSiswaAktif = $totalSiswa > 0

            ? round(
                ($siswaAktif / $totalSiswa) * 100
            )

            : 0;

        $persenKegiatanTervalidasi = $totalReports > 0

            ? round(
                ($totalEvaluated / $totalReports) * 100
            )

            : 0;

        // DISTRIBUSI PENGGUNA PER ROLE
        $totalAdmin = User::where(
            'role',
            'admin'
        )->count();

        // LOG AKTIVITAS SEDERHANA (BERDASARKAN AKUN TERBARU DIBUAT)
        $recentActivity = User::latest()

            ->take(5)

            ->get([
                'id',
                'name',
                'role',
                'created_at'
            ])

            ->map(function ($user) {

                $label = match ($user->role) {

                    'guru' => 'Akun guru',

                    'siswa' => 'Akun siswa',

                    default => 'Akun admin',
                };

                return [

                    'description' =>
                        "{$label} {$user->name} berhasil dibuat",

                    'waktu' =>
                        $user->created_at
                            ->diffForHumans(),
                ];
            })

            ->values();

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

                    'draft_reports' =>
                        $totalDraft,

                    'submitted_reports' =>
                        $totalSubmitted,

                    'evaluated_reports' =>
                        $totalEvaluated,

                    'average_compliance' =>
                        $averageCompliance,

                    'total_admin' =>
                        $totalAdmin,

                    'persen_guru_aktif' =>
                        $persenGuruAktif,

                    'persen_siswa_aktif' =>
                        $persenSiswaAktif,

                    'persen_kegiatan_tervalidasi' =>
                        $persenKegiatanTervalidasi,
                ],

                'recentReports' =>
                    $recentReports,

                'activityChart' =>
                    $activityChart,

                'recentActivity' =>
                    $recentActivity,

                'topStudents' =>
                    $topStudents,
            ]
        );
    }
}