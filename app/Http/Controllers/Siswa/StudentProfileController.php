<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\DataCorrection;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    // HALAMAN PROFIL
    public function index()
    {
        $profile = StudentProfile::with([
                    'schoolClass.teacher',
                    'user'
                ])

            ->firstOrCreate(
                ['user_id' => Auth::user()->id]
            );

        return Inertia::render('Siswa/Profile/Index', [

            'profile' => $profile
        ]);
    }

    // UPDATE PROFIL
    public function update(Request $request)
    {
        $validated = $request->validate([

            'religion' => 'nullable|string|max:100',

            'hobby' => 'nullable|string|max:255',

            'weight' => 'nullable|integer|min:1|max:300',

            'blood_type' => 'nullable|string|max:5',

            'favorite_food' => 'nullable|string|max:255',

            'favorite_subject' => 'nullable|string|max:255',

            'favorite_sport' => 'nullable|string|max:255',

            'strength' => 'nullable|string|max:1000',

            'weakness' => 'nullable|string|max:1000',
        ]);

        $profile = StudentProfile::firstOrCreate([
            'user_id' => Auth::user()->id
        ]);

        $profile->update($validated);

        // HITUNG ULANG PROFILE COMPLETION
        $fieldsCompletion = [
            'religion',
            'hobby',
            'weight',
            'blood_type',
            'favorite_food',
            'favorite_subject',
            'favorite_sport',
            'strength',
            'weakness',
        ];

        $filled = collect($fieldsCompletion)
            ->filter(fn ($field) => !empty($profile->$field))
            ->count();

        $profile->update([
            'profile_completion' => round(
                ($filled / count($fieldsCompletion)) * 100
            ),
        ]);

        return redirect()->back();
    }

    // LAPORKAN KESALAHAN DATA
    public function reportCorrection(Request $request)
    {
        $validated = $request->validate([

            'keterangan' =>
                'required|string|max:1000',
        ]);

        DataCorrection::create([

            'user_id' =>
                Auth::id(),

            'keterangan' =>
                $validated['keterangan'],
        ]);

        return redirect()->back()
            ->with(
                'success',
                'Laporan berhasil dikirim ke admin'
            );
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
}