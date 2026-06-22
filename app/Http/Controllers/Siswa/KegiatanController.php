<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatanHariIni = Kegiatan::where(
            'user_id',
            Auth::user()->id
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->first();

        // SUDAH SUBMIT HARI INI, ARAHKAN KE HALAMAN SUKSES
        if (
            $kegiatanHariIni
            && $kegiatanHariIni->status !== 'draft'
        ) {

            return redirect()->route(
                'siswa.kegiatan.success'
            );
        }

        $religion = Auth::user()
            ->studentProfile
            ?->religion;

        return Inertia::render(
            'Siswa/Kegiatan/Index',
            [
                'kegiatanHariIni' =>
                    $kegiatanHariIni,

                'religion' =>
                    $religion,
            ]
        );
    }

    public function selfie()
    {
        $kegiatan = Kegiatan::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (!$kegiatan) {
            return redirect()->route('siswa.kegiatan.index');
        }

        return Inertia::render('Siswa/Kegiatan/Selfie', [

            'kegiatanHariIni' =>
                $kegiatan,
        ]);
    }

    public function success()
    {
        $kegiatanHariIni = Kegiatan::where(
            'user_id',
            Auth::id()
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->first();

        if (!$kegiatanHariIni) {

            return redirect()->route(
                'siswa.kegiatan.index'
            );
        }

        return Inertia::render(
            'Siswa/Kegiatan/Succes',
            [
                'kegiatanHariIni' =>
                    $kegiatanHariIni,
            ]
        );
    }

    public function store(Request $request)
    {
        // CEK LAPORAN HARI INI
        $existing = Kegiatan::where(
            'user_id',
            Auth::user()->id
        )

        ->whereDate(
            'tanggal',
            now()->toDateString()
        )

        ->first();

        // HANYA DRAFT YANG BOLEH DIEDIT
        if (
            $existing &&
            $existing->status !== 'draft'
        ) {

            return back()->withErrors([

                'tanggal' =>
                    'Kegiatan hari ini sudah terkirim'
            ]);
        }

        // PROFILE SISWA
        $profile = Auth::user()
            ->studentProfile;

        // VALIDASI AGAMA
        if (
            (
                $request->detail_ibadah_centang ||
                $request->detail_ibadah_lain
            )
            &&
            !$profile?->religion
        ) {

            return back()->withErrors([

                'religion' =>
                    'Lengkapi data agama terlebih dahulu.'
            ]);
        }

        // VALIDASI
        $validated = $request->validate([

            'waktu_bangun' =>
                'nullable|date_format:H:i',

            'detail_ibadah_centang' =>
                'nullable|array',

            'detail_ibadah_lain' =>
                'nullable|string|max:1000',

            'menu_makan' =>
                'nullable|string|max:255',

            'jumlah_air' =>
                'nullable|integer|min:0',

            'jenis_olahraga' =>
                'nullable|string|max:255',

            'durasi_olahraga' =>
                'nullable|integer|min:0',

            'belajar_mandiri' =>
                'nullable|string|max:255',

            'durasi_belajar' =>
                'nullable|integer|min:0',

            'aktivitas_sosial' =>
                'nullable|string|max:1000',

            'waktu_tidur' =>
                'nullable|date_format:H:i',

            'keterangan_bangun' =>
                'nullable|string|max:500',

            'keterangan_tidur' =>
                'nullable|string|max:500',

            'bukti_foto_bangun' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto_ibadah' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto_makan' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto_olahraga' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto_belajar' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto_sosial' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bukti_foto' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'selfie_validasi' =>

                $request->status === 'submitted'

                    ? 'required|image|mimes:jpg,jpeg,png|max:2048'

                    : 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'status' =>
                'required|in:draft,submitted',
        ]);

        // GABUNGKAN DATA LAMA + DATA BARU (UNTUK HITUNG COMPLIANCE YANG AKURAT)
        $dataFinal = [

            'waktu_bangun' =>
                $validated['waktu_bangun']
                ?? $existing?->waktu_bangun,

            'detail_ibadah_centang' =>
                $validated['detail_ibadah_centang']
                ?? $existing?->detail_ibadah_centang,

            'menu_makan' =>
                $validated['menu_makan']
                ?? $existing?->menu_makan,

            'jenis_olahraga' =>
                $validated['jenis_olahraga']
                ?? $existing?->jenis_olahraga,

            'belajar_mandiri' =>
                $validated['belajar_mandiri']
                ?? $existing?->belajar_mandiri,

            'aktivitas_sosial' =>
                $validated['aktivitas_sosial']
                ?? $existing?->aktivitas_sosial,

            'waktu_tidur' =>
                $validated['waktu_tidur']
                ?? $existing?->waktu_tidur,
        ];

        // HITUNG COMPLIANCE
        $habitFields = [

            'waktu_bangun',

            'detail_ibadah_centang',

            'menu_makan',

            'jenis_olahraga',

            'belajar_mandiri',

            'aktivitas_sosial',

            'waktu_tidur',
        ];

        $filledHabits = 0;

        foreach ($habitFields as $field) {

            if (!empty($dataFinal[$field])) {

                $filledHabits++;
            }
        }

        $compliance = round(
            ($filledHabits / count($habitFields)) * 100
        );

        // FOTO BUKTI
        $buktiFoto = $existing?->bukti_foto;

        if ($request->hasFile('bukti_foto')) {

            if ($buktiFoto) {

                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($buktiFoto);
            }

            $buktiFoto = $request->file('bukti_foto')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoBangun = $existing?->bukti_foto_bangun;

        if ($request->hasFile('bukti_foto_bangun')) {

            if ($buktiFotoBangun) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoBangun);
            }

            $buktiFotoBangun = $request->file('bukti_foto_bangun')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoIbadah = $existing?->bukti_foto_ibadah;

        if ($request->hasFile('bukti_foto_ibadah')) {

            if ($buktiFotoIbadah) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoIbadah);
            }

            $buktiFotoIbadah = $request->file('bukti_foto_ibadah')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoMakan = $existing?->bukti_foto_makan;

        if ($request->hasFile('bukti_foto_makan')) {

            if ($buktiFotoMakan) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoMakan);
            }

            $buktiFotoMakan = $request->file('bukti_foto_makan')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoOlahraga = $existing?->bukti_foto_olahraga;

        if ($request->hasFile('bukti_foto_olahraga')) {

            if ($buktiFotoOlahraga) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoOlahraga);
            }

            $buktiFotoOlahraga = $request->file('bukti_foto_olahraga')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoBelajar = $existing?->bukti_foto_belajar;

        if ($request->hasFile('bukti_foto_belajar')) {

            if ($buktiFotoBelajar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoBelajar);
            }

            $buktiFotoBelajar = $request->file('bukti_foto_belajar')
                ->store('bukti-foto', 'public');
        }

        $buktiFotoSosial = $existing?->bukti_foto_sosial;

        if ($request->hasFile('bukti_foto_sosial')) {

            if ($buktiFotoSosial) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($buktiFotoSosial);
            }

            $buktiFotoSosial = $request->file('bukti_foto_sosial')
                ->store('bukti-foto', 'public');
        }

        // SELFIE VALIDASI
        $selfieValidasi = $existing?->selfie_validasi;

        if ($request->hasFile('selfie_validasi')) {

            if ($selfieValidasi) {

                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($selfieValidasi);
            }

            $selfieValidasi = $request->file('selfie_validasi')
                ->store('selfie-validasi', 'public');
        }

        // SIMPAN / UPDATE
        try {

            Kegiatan::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,

                    'tanggal' => now()->toDateString(),
                ],
                [
                    'waktu_bangun' => $dataFinal['waktu_bangun'],

                    'detail_ibadah_centang' => $dataFinal['detail_ibadah_centang'],

                    'detail_ibadah_lain' =>
                        $validated['detail_ibadah_lain']
                        ?? $existing?->detail_ibadah_lain,

                    'menu_makan' => $dataFinal['menu_makan'],

                    'jumlah_air' =>
                        $validated['jumlah_air']
                        ?? $existing?->jumlah_air,

                    'jenis_olahraga' => $dataFinal['jenis_olahraga'],

                    'durasi_olahraga' =>
                        $validated['durasi_olahraga']
                        ?? $existing?->durasi_olahraga,

                    'belajar_mandiri' => $dataFinal['belajar_mandiri'],

                    'durasi_belajar' =>
                        $validated['durasi_belajar']
                        ?? $existing?->durasi_belajar,

                    'aktivitas_sosial' => $dataFinal['aktivitas_sosial'],

                    'waktu_tidur' => $dataFinal['waktu_tidur'],

                    'bukti_foto' => $buktiFoto,

                    'bukti_foto_bangun' => $buktiFotoBangun,
                    'bukti_foto_ibadah' => $buktiFotoIbadah,
                    'bukti_foto_makan' => $buktiFotoMakan,
                    'bukti_foto_olahraga' => $buktiFotoOlahraga,
                    'bukti_foto_belajar' => $buktiFotoBelajar,
                    'bukti_foto_sosial' => $buktiFotoSosial,

                    'keterangan_bangun' =>
                        $validated['keterangan_bangun']
                        ?? $existing?->keterangan_bangun,

                    'keterangan_tidur' =>
                        $validated['keterangan_tidur']
                        ?? $existing?->keterangan_tidur,

                    'selfie_validasi' => $selfieValidasi,

                    'status' => $validated['status'],

                    'submitted_at' => $validated['status'] === 'submitted'

                            ? now()

                            : null,

                    'compliance_percentage' => $compliance,
                ]
            );

        } catch (\Illuminate\Database\QueryException $e) {

            return back()->withErrors([

                'tanggal' => 'Kegiatan hari ini sudah terkirim'
            ]);
        }

        if ($validated['status'] === 'submitted') {
                    return redirect()->route('siswa.kegiatan.success');
                }

                return redirect()->back();
    }

    public function history(Request $request)
    {
        $query = Kegiatan::where(
            'user_id',
            Auth::id()
        )

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        // FILTER TANGGAL
        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
        }

        // FILTER BULAN & TAHUN
                if ($request->filled('bulan')) {
                    $query->whereMonth('tanggal', $request->bulan);
                }

                if ($request->filled('tahun')) {
                    $query->whereYear('tanggal', $request->tahun);
                }

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

        $kegiatans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render(
            'Siswa/Riwayat/Index',
            [

                'kegiatans' =>
                    $kegiatans,

                'filters' => [

                    'tanggal' =>
                        $request->tanggal,

                    'status' =>
                        $request->status,

                    'nilai_guru' =>
                        $request->nilai_guru,

                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ]
            ]
        );
    }

    
    public function show(
        Kegiatan $kegiatan
    )
    {
        if (
            $kegiatan->user_id !== Auth::id()
        ) {

            abort(403);
        }

        if (
            $kegiatan->status === 'draft'
        ) {

            abort(404);
        }

        return Inertia::render(
                    'Siswa/Riwayat/Show',
                    [
                        'kegiatan' => $kegiatan
                    ]
                );
    }

}