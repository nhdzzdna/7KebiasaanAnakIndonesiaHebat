<?php

namespace App\Http\Controllers\Guru;


use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->input('mode', 'per_siswa');

        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        // MODE PER KEBIASAAN
        if ($mode === 'per_kebiasaan') {

            return $this->indexPerKebiasaan($request, $teacherClassId);
        }

        // MODE KALENDER
        if ($mode === 'kalender') {

            return $this->indexKalender($request, $teacherClassId);
        }

        // MODE PER SISWA (DEFAULT)
        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ])

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        if ($teacherClassId) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($teacherClassId) {

                    $q->where(
                        'school_class_id',
                        $teacherClassId
                    );
                }
            );
        }

        // FILTER TANGGAL — default ke hari ini, sesuai label kolom "Kebiasaan Hari Ini"
        $tanggalFilter = $request->input('tanggal', now()->toDateString());

        $query->whereDate(
            'tanggal',
            $tanggalFilter
        );

        // FILTER NILAI
        if ($request->filled('nilai_guru')) {

            $query->where(
                'nilai_guru',
                $request->nilai_guru
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

        // SORT NAMA SISWA (A-Z) — join ke tabel users karena nama bukan kolom di kegiatans
        $query->join(
            'users',
            'kegiatans.user_id',
            '=',
            'users.id'
        )
        ->orderBy(
            'users.name'
        )
        ->select(
            'kegiatans.*'
        );

        $kegiatans = $query
            ->paginate(10)
            ->withQueryString();

        // HITUNG STREAK, TOTAL HARI, & GRAFIK MINGGUAN PER BARIS
        // (cuma untuk siswa di halaman ini, bukan semua siswa di kelas)
        $kegiatans->getCollection()->transform(function ($k) {
            $k->streak = $this->hitungStreak($k->user_id);
            $k->total_hari = $this->hitungTotalHariBulanIni($k->user_id);
            $k->grafik_minggu = $this->hitungGrafikMinggu($k->user_id);
            return $k;
        });

        return Inertia::render(
            'Guru/Monitoring/Index',
            [
                'mode' => $mode,
                'kegiatans' => $kegiatans,

                'filters' => [

                    'tanggal' =>
                        $tanggalFilter,

                    'nilai_guru' =>
                        $request->nilai_guru,

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

    private function indexPerKebiasaan(Request $request, ?int $teacherClassId)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        // TOTAL SISWA DI KELAS (bukan dari jumlah kegiatan yang masuk)
        $totalSiswaKelas = \App\Models\StudentProfile::where(
            'school_class_id',
            $teacherClassId
        )->count();

        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ])

        ->whereDate('tanggal', $tanggal)

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        if ($teacherClassId) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($teacherClassId) {

                    $q->where(
                        'school_class_id',
                        $teacherClassId
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
            ->map(function ($label, $field) use ($kegiatans, $totalSiswaKelas) {

                $siswaList = $kegiatans->map(function ($k) use ($field) {

                    return [
                        'id_kegiatan' => $k->id,

                        'nama_siswa' => $k->user?->name,

                        'terisi' => !empty($k->$field),

                        'nilai' => $k->$field,
                    ];
                })->values();

                return [
                    'field' => $field,

                    'label' => $label,

                    'total_terisi' => $siswaList
                        ->where('terisi', true)
                        ->count(),

                    'total_siswa' => $totalSiswaKelas,

                    'siswa' => $siswaList,
                ];
            })

            ->values();

        return Inertia::render(
            'Guru/Monitoring/Index',
            [

                'mode' => 'per_kebiasaan',

                'perKebiasaan' => $perKebiasaan,

                'filters' => [

                    'tanggal' => $tanggal,
                ]
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODE KALENDER
    |--------------------------------------------------------------------------
    */

    private function indexKalender(Request $request, ?int $teacherClassId)
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

        if ($teacherClassId) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($teacherClassId) {

                    $q->where(
                        'school_class_id',
                        $teacherClassId
                    );
                }
            );
        }

        $kegiatans = $query->get();

        $perTanggal = $kegiatans
            ->groupBy(fn ($k) => $k->tanggal->format('Y-m-d'))
            ->sortKeys()
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

        return Inertia::render(
            'Guru/Monitoring/Index',
            [

                'mode' => 'kalender',

                'perTanggal' => $perTanggal,

                'filters' => [

                    'bulan' => $bulan,

                    'tahun' => $tahun,
                ]
            ]
        );
    }

        /*
        |--------------------------------------------------------------------------
        | DETAIL LAPORAN
        |--------------------------------------------------------------------------
        */

        public function show(
            Kegiatan $kegiatan
        )
        {
            $teacherClassId = Auth::user()
                ?->teacherProfile
                ?->school_class_id;

            if (
                $kegiatan->user
                    ?->studentProfile
                    ?->school_class_id
                !== $teacherClassId
            ) {

                abort(403);
            }

            $kegiatan->load([

                'user.studentProfile.schoolClass'
            ]);

            $userId = $kegiatan->user_id;

            // KEPATUHAN BULAN INI
            $complianceBulanIni = round(

                Kegiatan::where(
                    'user_id',
                    $userId
                )

                ->whereMonth(
                    'tanggal',
                    now()->month
                )

                ->whereYear(
                    'tanggal',
                    now()->year
                )

                ->whereIn('status', [
                    'submitted',
                    'evaluated'
                ])

                ->avg('compliance_percentage')

                ?? 0
            );

            // TOTAL HARI TERCATAT BULAN INI
            $totalHariBulanIni = $this->hitungTotalHariBulanIni($userId);

            // STREAK (HARI BERTURUT-TURUT TERAKHIR)
            $streak = $this->hitungStreak($userId);

            // GRAFIK PERKEMBANGAN 12 HARI TERAKHIR
            $twelveDaysAgo = now()->subDays(11)->startOfDay();

            $recentKegiatan = Kegiatan::where(
                'user_id',
                $userId
            )

            ->whereBetween('tanggal', [
                $twelveDaysAgo->toDateString(),
                now()->toDateString()
            ])

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->get()

            ->keyBy(fn ($k) => $k->tanggal->format('Y-m-d'));

            $chart12Hari = collect(range(0, 11))
                ->map(function ($i) use ($twelveDaysAgo, $recentKegiatan) {

                    $date = $twelveDaysAgo->copy()->addDays($i);

                    $dateKey = $date->format('Y-m-d');

                    $report = $recentKegiatan->get($dateKey);

                    return [
                        'tanggal' => $dateKey,

                        'compliance' =>
                            $report?->compliance_percentage
                            ?? 0,
                    ];
                })

                ->values();

            return Inertia::render(
                'Guru/Monitoring/Show',
                [

                    'kegiatan' =>
                        $kegiatan,

                    'siswaSummary' => [

                        'compliance_bulan_ini' =>
                            $complianceBulanIni,

                        'streak' =>
                            $streak,

                        'total_hari_bulan_ini' =>
                            $totalHariBulanIni,

                        'status_akun' =>
                            $kegiatan->user
                                ?->status_akun,
                    ],

                    'chart12Hari' =>
                        $chart12Hari,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EVALUASI LAPORAN
        |--------------------------------------------------------------------------
        */
        
        public function evaluasi(
            Request $request,
            Kegiatan $kegiatan
        )
        {
            $teacherClassId = Auth::user()
                ?->teacherProfile
                ?->school_class_id;

            if (
                $kegiatan->user
                    ?->studentProfile
                    ?->school_class_id
                !== $teacherClassId
            ) {

                abort(403);
            }

            if ($kegiatan->status === 'draft') {

                return back()->withErrors([

                    'kegiatan' =>
                        'Laporan draft tidak dapat dievaluasi.'
                ]);
            }

            if ($kegiatan->status === 'evaluated') {

                return back()->withErrors([

                    'kegiatan' =>
                        'Laporan sudah dievaluasi.'
                ]);
            }

            $validated = $request->validate([

                'nilai_guru' =>
                    'required|in:A,B,C,D',

                'catatan_guru' =>
                    'nullable|string'
            ]);

            $kegiatan->update([

                'nilai_guru' =>
                    $validated['nilai_guru'],

                'catatan_guru' =>
                    $validated['catatan_guru']
                    ?? null,

                'status' =>
                    'evaluated'
            ]);

            return back();
        }    
        
        /*
        |--------------------------------------------------------------------------
        | HELPER: HITUNG STREAK (dipakai index per_siswa & show)
        |--------------------------------------------------------------------------
        */
        private function hitungStreak(int $userId): int
        {
            $streak = 0;

            $tanggalCursor = now()->copy();

            while (true) {

                $ada = Kegiatan::where(
                    'user_id',
                    $userId
                )

                ->whereDate(
                    'tanggal',
                    $tanggalCursor->toDateString()
                )

                ->whereIn('status', [
                    'submitted',
                    'evaluated'
                ])

                ->exists();

                if (!$ada) {
                    break;
                }

                $streak++;

                $tanggalCursor->subDay();
            }

            return $streak;
        }

        /*
        |--------------------------------------------------------------------------
        | HELPER: TOTAL HARI TERCATAT BULAN INI
        |--------------------------------------------------------------------------
        */
        private function hitungTotalHariBulanIni(int $userId): int
        {
            return Kegiatan::where(
                'user_id',
                $userId
            )

            ->whereMonth(
                'tanggal',
                now()->month
            )

            ->whereYear(
                'tanggal',
                now()->year
            )

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | HELPER: GRAFIK 7 HARI TERAKHIR (untuk panel kanan Index)
        |--------------------------------------------------------------------------
        */
        private function hitungGrafikMinggu(int $userId): array
        {
            $sevenDaysAgo = now()->subDays(6)->startOfDay();

            $recentKegiatan = Kegiatan::where(
                'user_id',
                $userId
            )

            ->whereBetween('tanggal', [
                $sevenDaysAgo->toDateString(),
                now()->toDateString()
            ])

            ->whereIn('status', [
                'submitted',
                'evaluated'
            ])

            ->get()

            ->keyBy(fn ($k) => $k->tanggal->format('Y-m-d'));

            return collect(range(0, 6))
                ->map(function ($i) use ($sevenDaysAgo, $recentKegiatan) {

                    $dateKey = $sevenDaysAgo->copy()->addDays($i)->format('Y-m-d');

                    $report = $recentKegiatan->get($dateKey);

                    return $report?->compliance_percentage ?? 0;
                })

                ->values()

                ->all();
        }
}