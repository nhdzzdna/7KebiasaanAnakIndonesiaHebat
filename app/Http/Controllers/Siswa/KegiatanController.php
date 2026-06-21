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

        return Inertia::render(
            'Siswa/Kegiatan/Index',
            [
                'kegiatanHariIni' =>
                    $kegiatanHariIni
            ]
        );
    }

    public function selfie()
    {
        return Inertia::render('Siswa/Kegiatan/Selfie');
    }

    public function success()
    {
        return Inertia::render('Siswa/Kegiatan/Succes');
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

            'bukti_foto' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'selfie_validasi' =>

                $request->status === 'submitted'

                    ? 'required|image|mimes:jpg,jpeg,png|max:2048'

                    : 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'status' =>
                'required|in:draft,submitted',
        ]);

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

            if (!empty($validated[$field])) {

                $filledHabits++;
            }
        }

        $compliance = round(
            ($filledHabits / count($habitFields)) * 100
        );

        // FOTO BUKTI
        $buktiFoto = $existing?->bukti_foto;

        if ($request->hasFile('bukti_foto')) {

            $buktiFoto = $request->file('bukti_foto')
                ->store('bukti-foto', 'public');
        }

        // SELFIE VALIDASI
        $selfieValidasi = $existing?->selfie_validasi;

        if ($request->hasFile('selfie_validasi')) {

            $selfieValidasi = $request->file('selfie_validasi')
                ->store('selfie-validasi', 'public');
        }

        // SIMPAN / UPDATE
        Kegiatan::updateOrCreate(
            [
                'user_id' =>
                    Auth::user()->id,

                'tanggal' =>
                    now()->toDateString(),
            ],
            [
                'waktu_bangun' =>
                    $validated['waktu_bangun'],

                'detail_ibadah_centang' =>
                    $validated['detail_ibadah_centang'],

                'detail_ibadah_lain' =>
                    $validated['detail_ibadah_lain'],

                'menu_makan' =>
                    $validated['menu_makan'],

                'jumlah_air' =>
                    $validated['jumlah_air'],

                'jenis_olahraga' =>
                    $validated['jenis_olahraga'],

                'durasi_olahraga' =>
                    $validated['durasi_olahraga'],

                'belajar_mandiri' =>
                    $validated['belajar_mandiri'],

                'durasi_belajar' =>
                    $validated['durasi_belajar'],

                'aktivitas_sosial' =>
                    $validated['aktivitas_sosial'],

                'waktu_tidur' =>
                    $validated['waktu_tidur'],

                'bukti_foto' =>
                    $buktiFoto,

                'selfie_validasi' =>
                    $selfieValidasi,

                'status' =>
                    $validated['status'],

                'submitted_at' =>

                    $validated['status'] === 'submitted'

                        ? now()

                        : null,

                'compliance_percentage' =>
                    $compliance,
            ]
        );

        return redirect()->back();
    }

    public function history(Request $request)
    {
        $query = Kegiatan::where(
            'user_id',
            Auth::user()->id
        );

        // FILTER TANGGAL
        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
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
                ]
            ]
        );
    }
}