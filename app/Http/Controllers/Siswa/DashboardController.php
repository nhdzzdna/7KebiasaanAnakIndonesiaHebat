<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $userId = $user->id;

        // LAPORAN HARI INI
        $todayReport = Kegiatan::where(
            'user_id',
            $userId
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->first();

        // STATUS HARI INI
        $todayStatus =
            $todayReport?->status
            ?? 'belum_membuat';

        $isTodaySubmitted =
            in_array(
                $todayStatus,
                ['submitted', 'evaluated']
            );

        $isTodayEvaluated =
            $todayStatus === 'evaluated';

        // COMPLIANCE HARI INI
        $todayCompliance =
            $todayReport?->compliance_percentage
            ?? 0;

        // JUMLAH KEBIASAAN TERISI HARI INI
        $habitFields = [
            'waktu_bangun',
            'detail_ibadah_centang',
            'menu_makan',
            'jenis_olahraga',
            'belajar_mandiri',
            'aktivitas_sosial',
            'waktu_tidur',
        ];

        $habitsFilledToday = collect($habitFields)
            ->filter(fn ($field) =>
                !empty($todayReport?->$field)
            )

            ->count();

        // TOTAL LAPORAN
        $totalReports = Kegiatan::where(
            'user_id',
            $userId
        )->count();

        // TOTAL SUBMITTED
        $totalSubmitted = Kegiatan::where(
            'user_id',
            $userId
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->count();

        // RATA-RATA COMPLIANCE
        $averageCompliance = round(

            Kegiatan::where(
                'user_id',
                $userId
            )

            ->avg('compliance_percentage')

            ?? 0
        );

        // PROFILE COMPLETION
        $profileCompletion =
            $user->studentProfile?->profile_completion
            ?? 0;

        $profileWarning =
            $profileCompletion < 100;

        // EVALUASI TERAKHIR
        $latestEvaluation = Kegiatan::where(
            'user_id',
            $userId
        )

        ->where(
            'status',
            'evaluated'
        )

        ->whereNotNull('nilai_guru')

        ->latest()

        ->first();

        // FEEDBACK TERAKHIR
        $latestFeedback =
            $latestEvaluation?->catatan_guru;

        // NAMA WALI KELAS (UNTUK DITAMPILKAN DI CATATAN GURU)
        $namaWaliKelas =
            $user->studentProfile
                ?->schoolClass
                ?->teacher
                ?->name;

        // RIWAYAT TERBARU
        $recentReports = Kegiatan::where(
            'user_id',
            $userId
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->latest()

        ->take(5)

        ->get();

        // STREAK
        $submittedDates = Kegiatan::where(
            'user_id',
            $userId
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->orderByDesc('tanggal')

        ->pluck('tanggal')
        ->unique()

        ->map(fn ($date) => $date->format('Y-m-d'))

        ->toArray();

        $streak = 0;

        $currentDate = now();

        foreach ($submittedDates as $date) {

            if ($date === $currentDate->format('Y-m-d')) {

                $streak++;

                $currentDate->subDay();

            } else {

                break;
            }
        }

        // GRAFIK MINGGUAN (SENIN - MINGGU MINGGU INI)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyData = Kegiatan::where(
            'user_id',
            $userId
        )

        ->whereBetween('tanggal', [
            $startOfWeek->toDateString(),
            $endOfWeek->toDateString()
        ])

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->get()

        ->keyBy(fn ($k) => $k->tanggal->format('Y-m-d'));

        $weeklyChart = collect(range(0, 6))
            ->map(function ($i) use ($startOfWeek, $weeklyData) {

                $date = $startOfWeek->copy()->addDays($i);

                $dateKey = $date->format('Y-m-d');

                $report = $weeklyData->get($dateKey);

                return [
                    'hari' => $date->translatedFormat('D'),

                    'tanggal' => $dateKey,

                    'compliance' =>
                        $report?->compliance_percentage
                        ?? 0,
                ];
            })

            ->values();

        return Inertia::render(
            'Siswa/Dashboard',
            [

                'stats' => [

                    'total_reports' =>
                        $totalReports,

                    'total_submitted' =>
                        $totalSubmitted,

                    'average_compliance' =>
                        $averageCompliance,

                    'profile_completion' =>
                        $profileCompletion,

                    'streak' =>
                        $streak,

                    'habits_filled_today' =>
                        $habitsFilledToday,

                    'habits_total' => 7,
                ],

                'todayReport' =>
                    $todayReport,

                'todayStatus' =>
                    $todayStatus,

                'isTodaySubmitted' =>
                    $isTodaySubmitted,

                'isTodayEvaluated' =>
                    $isTodayEvaluated,

                'todayCompliance' =>
                    $todayCompliance,

                'profileWarning' =>
                    $profileWarning,

                'latestEvaluation' =>
                    $latestEvaluation,

                'latestFeedback' =>
                    $latestFeedback,

                'namaWaliKelas' =>
                    $namaWaliKelas,

                'recentReports' =>
                    $recentReports,

                'weeklyChart' =>
                    $weeklyChart,
            ]
        );
    }
}