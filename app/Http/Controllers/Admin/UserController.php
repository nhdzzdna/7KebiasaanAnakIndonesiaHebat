<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Inertia\Inertia;

class UserController extends Controller
{
    // LIST USER
    public function index(Request $request)
    {
        $query = User::query();

        // SEARCH
        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'name',
                    'like',
                    '%' . $request->search . '%'
                )

                ->orWhere(
                    'email',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        // FILTER ROLE
        if ($request->filled('role')) {

            $query->where(
                'role',
                $request->role
            );
        }

        // FILTER STATUS AKUN
        if ($request->filled('status_akun')) {

            $query->where(
                'status_akun',
                $request->status_akun
            );
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // DATA KELAS
        $classes = SchoolClass::get([
            'id',
            'name'
        ]);

        return Inertia::render(
            'Admin/Users/Index',
            [

                'users' =>
                    $users,

                'classes' =>
                    $classes,

                'filters' => [

                    'search' =>
                        $request->search,

                    'role' =>
                        $request->role,

                    'status_akun' =>
                        $request->status_akun,
                ]
            ]
        );
    }

    // TAMBAH USER
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' =>
                'required|string|max:255',

            'email' =>
                'required|email|unique:users,email',

            'password' =>
                'required|min:6',

            'role' =>
                'required|in:admin,guru,siswa',

            'school_class_id' =>
                'nullable|exists:school_classes,id',
        ]);

        $user = User::create([

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role' =>
                $validated['role'],

            'status_akun' =>
                'aktif',
        ]);

        // PROFILE SISWA
        if ($validated['role'] === 'siswa') {

            StudentProfile::create([

                'user_id' =>
                    $user->id,

                'school_class_id' =>
                    $validated['school_class_id']
                    ?? null,
            ]);
        }

        // PROFILE GURU
        if ($validated['role'] === 'guru') {

            TeacherProfile::create([

                'user_id' =>
                    $user->id,

                'school_class_id' =>
                    $validated['school_class_id']
                    ?? null,
            ]);
        }

        return redirect()->back();
    }

    // UPDATE USER
public function update(
    Request $request,
    User $user
) {

    $oldRole = $user->role;

    $validated = $request->validate([

        'name' =>
            'required|string|max:255',

        'email' =>
            'required|email|unique:users,email,' . $user->id,

        'role' =>
            'required|in:admin,guru,siswa',

        'status_akun' =>
            'required|in:aktif,nonaktif',

        'school_class_id' =>
            'nullable|exists:school_classes,id',
    ]);

    // UPDATE USER
    $user->update([

        'name' =>
            $validated['name'],

        'email' =>
            $validated['email'],

        'role' =>
            $validated['role'],

        'status_akun' =>
            $validated['status_akun'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | ROLE TRANSITION CLEANUP
    |--------------------------------------------------------------------------
    */

    // SISWA -> BUKAN SISWA
    if (
        $oldRole === 'siswa'
        &&
        $validated['role'] !== 'siswa'
    ) {

        $user->studentProfile()?->delete();
    }

    // GURU -> BUKAN GURU
    if (
        $oldRole === 'guru'
        &&
        $validated['role'] !== 'guru'
    ) {

        $user->teacherProfile()?->delete();

        // RESET WALI KELAS
        \App\Models\SchoolClass::where(
            'teacher_id',
            $user->id
        )->update([

            'teacher_id' => null
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE / UPDATE PROFILE BARU
    |--------------------------------------------------------------------------
    */

    // SISWA
    if ($validated['role'] === 'siswa') {

        StudentProfile::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'school_class_id' =>
                    $validated['school_class_id']
            ]
        );
    }

    // GURU
    if ($validated['role'] === 'guru') {

        TeacherProfile::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'school_class_id' =>
                    $validated['school_class_id']
            ]
        );
    }

    return redirect()->back();
}

    // DELETE USER
    public function destroy(User $user)
    {
        // CEGAH HAPUS ADMIN SENDIRI
        if (
            \Illuminate\Support\Facades\Auth::id() === $user->id
        ){

            return back()->withErrors([

                'user' =>
                    'Tidak bisa menghapus akun sendiri.'
            ]);
        }

        $user->delete();

        return redirect()->back();
    }
} 