<?php

namespace App\Http\Controllers\Guru;

use App\Exports\RekapExport;
use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN REKAP & LAPORAN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $query = Kegiatan::with('user.studentProfile.schoolClass')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereIn('status', ['submitted', 'evaluated'])
            ->orderBy('tanggal');

        if ($teacherClassId) {

            $query->whereHas('user.studentProfile', function ($q) use ($teacherClassId) {

                $q->where('school_class_id', $teacherClassId);
            });
        }

        $kegiatans = $query->get();

        // REKAP PER SISWA
        $habitFieldsRekap = [
            'waktu_bangun' => 'bangun',
            'jenis_olahraga' => 'olahraga',
            'menu_makan' => 'makan',
            'belajar_mandiri' => 'belajar',
            'detail_ibadah_centang' => 'ibadah',
            'waktu_tidur' => 'tidur',
            'aktivitas_sosial' => 'sosial',
        ];

        $rekapSiswa = $kegiatans
            ->groupBy('user_id')
            ->map(function ($items) use ($habitFieldsRekap) {

                $user = $items->first()->user;

                $totalHari = $items->count();

                // PERSENTASE PER KEBIASAAN
                $perKebiasaan = collect($habitFieldsRekap)
                    ->mapWithKeys(function ($key, $field) use ($items, $totalHari) {

                        $terisi = $items->filter(
                            fn($k) => !empty($k->$field)
                        )->count();

                        $persen = $totalHari > 0

                            ? round(
                                ($terisi / $totalHari) * 100
                            )

                            : 0;

                        return [$key => $persen];
                    });

                return [
                    'id_siswa' => $user->id,
                    'nama' => $user->name,
                    'total_hari' => $totalHari,
                    'rata_rata_kepatuhan' => round($items->avg('compliance_percentage')),
                    'nilai_akhir' => $items->whereNotNull('nilai_guru')->last()?->nilai_guru ?? '-',
                    'bangun' => $perKebiasaan['bangun'],
                    'olahraga' => $perKebiasaan['olahraga'],
                    'makan' => $perKebiasaan['makan'],
                    'belajar' => $perKebiasaan['belajar'],
                    'ibadah' => $perKebiasaan['ibadah'],
                    'tidur' => $perKebiasaan['tidur'],
                    'sosial' => $perKebiasaan['sosial'],
                ];
            })
            ->values();

        // RATA-RATA KEPATUHAN KELAS
        $rataKelas = round($kegiatans->avg('compliance_percentage') ?? 0);

        // SISWA BERPRESTASI (>= 90%)
        $siswaBerprestasi = $rekapSiswa
            ->where('rata_rata_kepatuhan', '>=', 90)
            ->count();

        // SISWA PERLU BIMBINGAN (< 60%)
        $siswaPerluBimbingan = $rekapSiswa
            ->where('rata_rata_kepatuhan', '<', 60)
            ->count();

        // SUDAH / BELUM DIEVALUASI
        $sudahDievaluasi = $kegiatans
            ->whereNotNull('nilai_guru')
            ->unique('user_id')
            ->count();

        $belumDievaluasi = $rekapSiswa->count() - $sudahDievaluasi;

        // KEBIASAAN TERLEMAH KELAS
        $habitFields = [
            'waktu_bangun' => 'Bangun Pagi',
            'detail_ibadah_centang' => 'Ibadah & Doa',
            'menu_makan' => 'Makan Sehat',
            'jenis_olahraga' => 'Olahraga',
            'belajar_mandiri' => 'Belajar Mandiri',
            'aktivitas_sosial' => 'Aktivitas Sosial',
            'waktu_tidur' => 'Tidur Cepat',
        ];

        $totalData = $kegiatans->count();

        $kebiasaanTerlemah = collect($habitFields)
            ->map(function ($label, $field) use ($kegiatans, $totalData) {

                $filled = $kegiatans->filter(function ($k) use ($field) {

                    return !empty($k->$field);
                })->count();

                return [
                    'kebiasaan' => $label,
                    'persentase' => $totalData > 0
                        ? round(($filled / $totalData) * 100)
                        : 0,
                ];
            })
            ->sortBy('persentase')
            ->values();

        // TREN KEPATUHAN PER PERIODE (7 bulan terakhir)
        $trenKepatuhan = collect(range(0, 6))
            ->map(function ($i) use ($teacherClassId) {

                $date = now()->subMonths($i);

                $query = Kegiatan::whereMonth('tanggal', $date->month)

                    ->whereYear('tanggal', $date->year)

                    ->whereIn('status', ['submitted', 'evaluated']);

                if ($teacherClassId) {

                    $query->whereHas('user.studentProfile', function ($q) use ($teacherClassId) {

                        $q->where('school_class_id', $teacherClassId);
                    });
                }

                return [
                    'bulan' => $date->translatedFormat('M Y'),
                    'rata_rata' => round($query->avg('compliance_percentage') ?? 0),
                ];
            })
            ->reverse()
            ->values();

        return Inertia::render('Guru/Rekap/Index', [

            'rekapSiswa' => $rekapSiswa,

            'stats' => [
                'rata_rata_kelas' => $rataKelas,
                'siswa_berprestasi' => $siswaBerprestasi,
                'siswa_perlu_bimbingan' => $siswaPerluBimbingan,
                'sudah_dievaluasi' => $sudahDievaluasi,
                'belum_dievaluasi' => $belumDievaluasi,
            ],

            'kebiasaanTerlemah' => $kebiasaanTerlemah,

            'trenKepatuhan' => $trenKepatuhan,

            'filters' => [
                'bulan' => (int) $bulan,
                'tahun' => (int) $tahun,
                'kelas' => Auth::user()
                    ?->teacherProfile
                    ?->schoolClass
                    ?->name
                    ?? '-',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf(Request $request)
    {
        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $query = Kegiatan::with('user.studentProfile.schoolClass')

            ->whereMonth('tanggal', $bulan)

            ->whereYear('tanggal', $tahun)

            ->whereIn('status', ['submitted', 'evaluated'])
            
            ->orderBy('tanggal');

        if ($teacherClassId) {

            $query->whereHas('user.studentProfile', function ($q) use ($teacherClassId) {

                $q->where('school_class_id', $teacherClassId);
            });
        }

        $kegiatans = $query->get();

        $habitFieldsRekap = [
            'waktu_bangun' => 'bangun',
            'jenis_olahraga' => 'olahraga',
            'menu_makan' => 'makan',
            'belajar_mandiri' => 'belajar',
            'detail_ibadah_centang' => 'ibadah',
            'waktu_tidur' => 'tidur',
            'aktivitas_sosial' => 'sosial',
        ];

        $data = $kegiatans
            ->groupBy('user_id')
            ->map(function ($items) use ($habitFieldsRekap) {

                $user = $items->first()->user;

                $totalHari = $items->count();

                $perKebiasaan = collect($habitFieldsRekap)
                    ->mapWithKeys(function ($key, $field) use ($items, $totalHari) {

                        $terisi = $items->filter(
                            fn($k) => !empty($k->$field)
                        )->count();

                        $persen = $totalHari > 0

                            ? round(
                                ($terisi / $totalHari) * 100
                            )

                            : 0;

                        return [$key => $persen];
                    });

                return [
                    'nama' => $user->name,
                    'kelas' => $user->studentProfile?->schoolClass?->name ?? '-',
                    'total_hari' => $totalHari,
                    'rata_rata_kepatuhan' => round($items->avg('compliance_percentage')),
                    'nilai_akhir' => $items->whereNotNull('nilai_guru')->last()?->nilai_guru ?? '-',
                    'bangun' => $perKebiasaan['bangun'],
                    'olahraga' => $perKebiasaan['olahraga'],
                    'makan' => $perKebiasaan['makan'],
                    'belajar' => $perKebiasaan['belajar'],
                    'ibadah' => $perKebiasaan['ibadah'],
                    'tidur' => $perKebiasaan['tidur'],
                    'sosial' => $perKebiasaan['sosial'],
                ];
            })
            ->values();

        $namaKelas = $data->first()['kelas'] ?? null;

        $pdf = Pdf::loadView('exports.rekap-pdf', [
            'data' => $data,
            'namaBulan' => \Carbon\Carbon::create()->month($bulan)->translatedFormat('F'),
            'tahun' => $tahun,
            'namaKelas' => $namaKelas,
        ]);

        return $pdf->download("rekap-kegiatan-{$bulan}-{$tahun}.pdf");
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportExcel(Request $request)
    {
        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        return Excel::download(
            new RekapExport($bulan, $tahun, $teacherClassId),
            "rekap-kegiatan-{$bulan}-{$tahun}.xlsx"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DATA GRAFIK (untuk dashboard/rekap chart)
    |--------------------------------------------------------------------------
    */

    public function grafik(Request $request)
    {
        $teacherClassId = Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $query = Kegiatan::whereMonth('tanggal', $bulan)

            ->whereYear('tanggal', $tahun)

            ->whereIn('status', ['submitted', 'evaluated']);

        if ($teacherClassId) {

            $query->whereHas('user.studentProfile', function ($q) use ($teacherClassId) {

                $q->where('school_class_id', $teacherClassId);
            });
        }

        $kegiatans = $query->get();

        $perTanggal = $kegiatans
            ->groupBy(fn($k) => $k->tanggal->format('Y-m-d'))
            ->map(function ($items, $tanggal) {

                return [
                    'tanggal' => $tanggal,
                    'rata_rata' => round($items->avg('compliance_percentage')),
                ];
            })
            ->values();

        return response()->json([
            'perTanggal' => $perTanggal,
        ]);
    }
}
