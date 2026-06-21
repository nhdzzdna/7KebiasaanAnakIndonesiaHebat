<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        if (!$teacherClassId) {

            return Inertia::render(
                'Guru/Dashboard',
                [

                    'stats' => [

                        'total_students' => 0,
                        'today_reports' => 0,
                        'average_compliance' => 0,
                        'pending_evaluations' => 0,
                        'submission_rate' => 0,
                    ],

                    'topPerformers' => [],
                    'needAttention' => [],
                    'notSubmitted' => [],
                    'latestEvaluations' => [],
                    'topActive' => [],
                    'classWeeklyProgress' => [],
                    'classMonthlyChart' => [],
                ]
            );
        }

        // SISWA DALAM KELAS
        $students = StudentProfile::with('user')

            ->where(
                'school_class_id',
                $teacherClassId
            )

            ->get();

        $studentIds = $students
            ->pluck('user_id');

        // TOTAL SISWA
        $totalStudents = $students->count();

        // LAPORAN HARI INI
        $todayReports = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->count();

        // PERSENTASE SUBMIT HARI INI
        $submissionRate = $totalStudents > 0

            ? round(
                ($todayReports / $totalStudents) * 100
            )

            : 0;

        // MENUNGGU EVALUASI
        $pendingEvaluations = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->where(
            'status',
            'submitted'
        )

        ->count();

        // RATA-RATA COMPLIANCE
        $averageCompliance = round(

            Kegiatan::whereIn(
                'user_id',
                $studentIds
            )

            ->avg('compliance_percentage')

            ?? 0
        );

        // TOP PERFORMER
        $topPerformers = Kegiatan::with('user')

            ->whereIn(
                'user_id',
                $studentIds
            )

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->orderByDesc(
                'compliance_percentage'
            )

            ->latest()

            ->take(5)

            ->get();

        // SISWA PERLU PERHATIAN
        $needAttention = Kegiatan::with('user')

            ->whereIn(
                'user_id',
                $studentIds
            )

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->where(
                'compliance_percentage',
                '<',
                60
            )

            ->latest()

            ->take(5)

            ->get();

        // BELUM SUBMIT
        $submittedToday = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->pluck('user_id');

        $notSubmitted = $students
            ->whereNotIn(
                'user_id',
                $submittedToday
            )
            ->values();

        // EVALUASI TERBARU
        $latestEvaluations = Kegiatan::with('user')

            ->whereIn(
                'user_id',
                $studentIds
            )

            ->whereNotNull('nilai_guru')

            ->latest()

            ->take(5)

            ->get();

        // SISWA TERAKTIF (BERDASARKAN JUMLAH HARI SUBMIT)
        $topActive = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->select('user_id')

        ->selectRaw('COUNT(*) as total_submit')

        ->groupBy('user_id')

        ->orderByDesc('total_submit')

        ->take(5)

        ->with('user')

        ->get();

        // PROGRES KEBIASAAN KELAS - MINGGU INI
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyKegiatan = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->whereBetween('tanggal', [
            $startOfWeek->toDateString(),
            $endOfWeek->toDateString()
        ])

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->get();

        $habitFields = [
            'waktu_bangun' => 'Bangun Pagi',
            'detail_ibadah_centang' => 'Ibadah & Doa',
            'menu_makan' => 'Makan Sehat & Bergizi',
            'jenis_olahraga' => 'Olahraga',
            'belajar_mandiri' => 'Belajar Mandiri',
            'aktivitas_sosial' => 'Aktivitas Sosial',
            'waktu_tidur' => 'Tidur Cepat',
        ];

        $totalLaporanMingguIni = $weeklyKegiatan->count();

        $classWeeklyProgress = collect($habitFields)
            ->map(function ($label, $field) use ($weeklyKegiatan, $totalLaporanMingguIni) {

                $terisi = $weeklyKegiatan
                    ->filter(fn ($k) => !empty($k->$field))
                    ->count();

                return [
                    'field' => $field,

                    'label' => $label,

                    'persentase' => $totalLaporanMingguIni > 0

                        ? round(
                            ($terisi / $totalLaporanMingguIni) * 100
                        )

                        : 0,
                ];
            })

            ->values();

        // PROFIL KEGIATAN KELAS - 30 HARI TERAKHIR
        $thirtyDaysAgo = now()->subDays(29)->startOfDay();

        $monthlyKegiatan = Kegiatan::whereIn(
            'user_id',
            $studentIds
        )

        ->whereBetween('tanggal', [
            $thirtyDaysAgo->toDateString(),
            now()->toDateString()
        ])

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ])

        ->get()

        ->groupBy(fn ($k) => $k->tanggal->format('Y-m-d'));

        $classMonthlyChart = collect(range(0, 29))
            ->map(function ($i) use ($thirtyDaysAgo, $monthlyKegiatan) {

                $date = $thirtyDaysAgo->copy()->addDays($i);

                $dateKey = $date->format('Y-m-d');

                $items = $monthlyKegiatan->get($dateKey);

                return [
                    'tanggal' => $dateKey,

                    'rata_rata_kepatuhan' =>
                        $items?->avg('compliance_percentage')

                            ? round($items->avg('compliance_percentage'))

                            : 0,
                ];
            })

            ->values();

        return Inertia::render(
            'Guru/Dashboard',
            [

                'stats' => [

                    'total_students' =>
                        $totalStudents,

                    'today_reports' =>
                        $todayReports,

                    'average_compliance' =>
                        $averageCompliance,

                    'pending_evaluations' =>
                        $pendingEvaluations,

                    'submission_rate' =>
                        $submissionRate,
                ],

                'topPerformers' =>
                    $topPerformers,

                'needAttention' =>
                    $needAttention,

                'notSubmitted' =>
                    $notSubmitted,

                'latestEvaluations' =>
                    $latestEvaluations,

                'topActive' =>
                    $topActive,

                'classWeeklyProgress' =>
                    $classWeeklyProgress,

                'classMonthlyChart' =>
                    $classMonthlyChart,
            ]
        );
    }
}