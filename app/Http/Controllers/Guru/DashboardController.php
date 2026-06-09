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
                ],

                'topPerformers' =>
                    $topPerformers,

                'needAttention' =>
                    $needAttention,

                'notSubmitted' =>
                    $notSubmitted,

                'latestEvaluations' =>
                    $latestEvaluations,
            ]
        );
    }
}