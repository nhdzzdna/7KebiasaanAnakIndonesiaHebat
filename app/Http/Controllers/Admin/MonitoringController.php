<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Kegiatan;
use App\Models\SchoolClass;

use Illuminate\Http\Request;

use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->input('mode', 'per_siswa');

        // MODE PER KEBIASAAN
        if ($mode === 'per_kebiasaan') {

            return $this->indexPerKebiasaan($request);
        }

        // MODE KALENDER
        if ($mode === 'kalender') {

            return $this->indexKalender($request);
        }

        // MODE PER SISWA (DEFAULT)
        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ]);

        // FILTER STATUS
        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        // FILTER NILAI
        if ($request->filled('nilai_guru')) {

            $query->where(
                'nilai_guru',
                $request->nilai_guru
            );
        }

        // FILTER TANGGAL
        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
        }

        // FILTER KELAS
        if ($request->filled('school_class_id')) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($request) {

                    $q->where(
                        'school_class_id',
                        $request->school_class_id
                    );
                }
            );
        }

        // SEARCH SISWA
        if ($request->filled('search')) {

            $query->whereHas(
                'user',
                function ($q) use ($request) {

                    $q->where(
                        'name',
                        'like',
                        '%' . $request->search . '%'
                    );
                }
            );
        }

        $kegiatans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // DATA KELAS
        $classes = SchoolClass::get([
            'id',
            'name'
        ]);

        // STATISTIK
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

        $topStudents = Kegiatan::with('user')

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->orderByDesc(
                'compliance_percentage'
            )

            ->take(10)

            ->get();

        $needGuidance = Kegiatan::with('user')

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

            ->take(10)

            ->get();

        $weeklyReports = Kegiatan::whereBetween(
            'tanggal',
            [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]
        )->count();

        $monthlyReports = Kegiatan::whereMonth(
            'tanggal',
            now()->month
        )

        ->whereYear(
            'tanggal',
            now()->year
        )

        ->count();

        return Inertia::render(
            'Admin/Monitoring/Index',
            [

                'mode' => $mode,

                'kegiatans' =>
                    $kegiatans,

                'classes' =>
                    $classes,

                'stats' => [

                    'draft' =>
                        $totalDraft,

                    'submitted' =>
                        $totalSubmitted,

                    'evaluated' =>
                        $totalEvaluated,

                    'weekly_reports' =>
                        $weeklyReports,

                    'monthly_reports' =>
                        $monthlyReports,
                ],

                'topStudents' =>
                    $topStudents,

                'needGuidance' =>
                    $needGuidance,

                'filters' => [

                    'status' =>
                        $request->status,

                    'nilai_guru' =>
                        $request->nilai_guru,

                    'tanggal' =>
                        $request->tanggal,

                    'school_class_id' =>
                        $request->school_class_id,

                    'search' =>
                        $request->search,
                ]
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODE PER KEBIASAAN
    |--------------------------------------------------------------------------
    */

    private function indexPerKebiasaan(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        // TOTAL SISWA (semua siswa, atau yang di kelas terpilih kalau difilter)
        $totalSiswaQuery = \App\Models\StudentProfile::query();

        if ($request->filled('school_class_id')) {

            $totalSiswaQuery->where(
                'school_class_id',
                $request->school_class_id
            );
        }

        $totalSiswaKeseluruhan = $totalSiswaQuery->count();

        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ])

        ->whereDate('tanggal', $tanggal)

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        // FILTER KELAS (OPSIONAL)
        if ($request->filled('school_class_id')) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($request) {

                    $q->where(
                        'school_class_id',
                        $request->school_class_id
                    );
                }
            );
        }

        $kegiatans = $query->get();

        $habitFields = [
            'waktu_bangun' => 'Bangun Pagi',
            'detail_ibadah_centang' => 'Ibadah & Doa',
            'menu_makan' => 'Makan Sehat & Bergizi',
            'jenis_olahraga' => 'Olahraga',
            'belajar_mandiri' => 'Belajar Mandiri',
            'aktivitas_sosial' => 'Aktivitas Sosial',
            'waktu_tidur' => 'Tidur Cepat',
        ];

        $perKebiasaan = collect($habitFields)
            ->map(function ($label, $field) use ($kegiatans, $totalSiswaKeseluruhan) {

                $siswaList = $kegiatans->map(function ($k) use ($field) {

                    return [
                        'id_kegiatan' => $k->id,

                        'nama_siswa' => $k->user?->name,

                        'kelas' => $k->user
                            ?->studentProfile
                            ?->schoolClass
                            ?->name,

                        'terisi' => !empty($k->$field),

                        'nilai' => $k->$field,
                    ];
                })->values();

                return [
                    'field' => $field,
                    'label' => $label,
                    'total_terisi' => $siswaList->where('terisi', true)->count(),
                    'total_siswa' => $totalSiswaKeseluruhan,
                    'siswa' => $siswaList,
                ];
            })
            ->values();

        $classes = SchoolClass::get([
            'id',
            'name'
        ]);

        return Inertia::render(
            'Admin/Monitoring/Index',
            [

                'mode' => 'per_kebiasaan',

                'perKebiasaan' => $perKebiasaan,

                'classes' => $classes,

                'filters' => [

                    'tanggal' => $tanggal,

                    'school_class_id' =>
                        $request->school_class_id,
                ]
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODE KALENDER
    |--------------------------------------------------------------------------
    */

    private function indexKalender(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);

        $tahun = (int) $request->input('tahun', now()->year);

        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ])

        ->whereMonth('tanggal', $bulan)

        ->whereYear('tanggal', $tahun)

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        // FILTER KELAS (OPSIONAL)
        if ($request->filled('school_class_id')) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($request) {

                    $q->where(
                        'school_class_id',
                        $request->school_class_id
                    );
                }
            );
        }

        $kegiatans = $query->get();

        $perTanggal = $kegiatans
            ->groupBy(fn ($k) => $k->tanggal->format('Y-m-d'))
            ->map(function ($items, $tanggal) {

                return [
                    'tanggal' => $tanggal,

                    'total_siswa_lapor' => $items->count(),

                    'rata_rata_kepatuhan' => round(
                        $items->avg('compliance_percentage')
                    ),
                ];
            })

            ->values();

        $classes = SchoolClass::get([
            'id',
            'name'
        ]);

        return Inertia::render(
            'Admin/Monitoring/Index',
            [

                'mode' => 'kalender',

                'perTanggal' => $perTanggal,

                'classes' => $classes,

                'filters' => [

                    'bulan' => $bulan,

                    'tahun' => $tahun,

                    'school_class_id' =>
                        $request->school_class_id,
                ]
            ]
        );
    }

    // DETAIL MONITORING
    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load([
            'user.studentProfile.schoolClass'
        ]);

        return Inertia::render(
            'Admin/Monitoring/Show',
            [
                'kegiatan' => $kegiatan
            ]
        );
    }
}