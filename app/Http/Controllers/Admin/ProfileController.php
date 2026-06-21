<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

use Inertia\Inertia;

class ProfileController extends Controller
{
    // HALAMAN PROFIL
    public function index()
    {
        return Inertia::render(
            'Admin/Profile/Index',
            [

                'user' =>
                    Auth::user(),

                'settings' => [

                    'notifikasi_email' =>
                        Setting::getValue(
                            'notifikasi_email',
                            '1'
                        ) === '1',

                    'laporan_otomatis' =>
                        Setting::getValue(
                            'laporan_otomatis',
                            '0'
                        ) === '1',

                    'alert_pengguna_baru' =>
                        Setting::getValue(
                            'alert_pengguna_baru',
                            '1'
                        ) === '1',
                ]
            ]
        );
    }

    // UPDATE DATA AKUN
    public function update(Request $request)
    {
        $validated = $request->validate([

            'name' =>
                'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . Auth::id(),
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update($validated);

        return redirect()->back();
    }

    // GANTI PASSWORD
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([

            'current_password' =>
                'required|current_password',

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([

            'password' =>
                Hash::make(
                    $validated['password']
                ),
        ]);

        return redirect()->back();
    }

    // UPLOAD FOTO PROFIL
    public function uploadFoto(Request $request)
    {
        $request->validate([

            'foto' =>
                'required|image|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->foto) {

            Storage::disk('public')
                ->delete($user->foto);
        }

        $path = $request->file('foto')
            ->store('foto-profil', 'public');

        $user->update([

            'foto' => $path,
        ]);

        return redirect()->back();
    }

    // UPDATE PENGATURAN SISTEM
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([

            'notifikasi_email' =>
                'required|boolean',

            'laporan_otomatis' =>
                'required|boolean',

            'alert_pengguna_baru' =>
                'required|boolean',
        ]);

        foreach ($validated as $key => $value) {

            Setting::setValue(
                $key,
                $value ? '1' : '0'
            );
        }

        return redirect()->back();
    }

    // RESET DATA KEGIATAN PADA RENTANG TANGGAL TERTENTU
    public function resetKegiatanData(Request $request)
    {
        $validated = $request->validate([

            'password' =>
                'required|current_password',

            'tanggal_mulai' =>
                'required|date',

            'tanggal_akhir' =>
                'required|date|after_or_equal:tanggal_mulai',
        ]);

        \App\Models\Kegiatan::whereBetween(
            'tanggal',
            [
                $validated['tanggal_mulai'],
                $validated['tanggal_akhir'],
            ]
        )->delete();

        return redirect()->back()
            ->with(
                'success',
                'Data kegiatan pada rentang tanggal tersebut berhasil dihapus'
            );
    }

    // NONAKTIFKAN SEMUA SISWA
    public function deactivateAllStudents(Request $request)
    {
        $validated = $request->validate([

            'password' =>
                'required|current_password',
        ]);

        \App\Models\User::where(
            'role',
            'siswa'
        )

        ->update([

            'status_akun' => 'nonaktif',
        ]);

        return redirect()->back()
            ->with(
                'success',
                'Semua akun siswa berhasil dinonaktifkan'
            );
    }
}