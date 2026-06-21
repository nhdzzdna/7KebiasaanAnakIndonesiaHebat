<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->load(
            'teacherProfile.schoolClass'
        );

        return Inertia::render(
            'Guru/Profile/Index',
            [

                'user' => $user,
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
}
