<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\DataCorrection;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

        // JUMLAH USER PER ROLE (UNTUK LABEL TAB)
        $totalSemua = User::count();

        $totalGuru = User::where(
            'role',
            'guru'
        )->count();

        $totalSiswa = User::where(
            'role',
            'siswa'
        )->count();

        return Inertia::render(
            'Admin/Users/Index',
            [

                'users' =>
                    $users,

                'classes' =>
                    $classes,

                'roleCounts' => [

                    'semua' =>
                        $totalSemua,

                    'guru' =>
                        $totalGuru,

                    'siswa' =>
                        $totalSiswa,
                ],

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
                'required|min:8',

            'role' =>
                'required|in:admin,guru,siswa',

            'school_class_id' =>
                'nullable|exists:school_classes,id',

            'birth_date' =>
                'nullable|date',

            'address' =>
                'nullable|string|max:1000',

            'parent_name' =>
                'nullable|string|max:255',

            'parent_phone' =>
                'nullable|string|max:50',
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

            'birth_date' =>
                $validated['birth_date']
                ?? null,

            'address' =>
                $validated['address']
                ?? null,

            'parent_name' =>
                $validated['parent_name']
                ?? null,

            'parent_phone' =>
                $validated['parent_phone']
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

            'birth_date' =>
                'nullable|date',

            'address' =>
                'nullable|string|max:1000',

            'parent_name' =>
                'nullable|string|max:255',

            'parent_phone' =>
                'nullable|string|max:50',
        ]);

        if (
            $user->isAdmin()
            &&
            $validated['role'] !== 'admin'
        ) {

            $adminCount = User::where(
                'role',
                'admin'
            )->count();

            if ($adminCount <= 1) {

                return back()->withErrors([

                    'user' =>
                        'Minimal harus ada satu admin.'
                ]);
            }
        }

        if (
            Auth::id() === $user->id
            &&
            $validated['status_akun'] === 'nonaktif'
        ) {

            return back()->withErrors([

                'user' =>
                    'Tidak bisa menonaktifkan akun sendiri.'
            ]);
        }

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
                        $validated['school_class_id'],

                    'birth_date' =>
                        $validated['birth_date']
                        ?? null,

                    'address' =>
                        $validated['address']
                        ?? null,

                    'parent_name' =>
                        $validated['parent_name']
                        ?? null,

                    'parent_phone' =>
                        $validated['parent_phone']
                        ?? null,
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

    public function resetPassword(User $user)
    {
        $user->update([

            'password' => Hash::make(
                'password123'
            )
        ]);

        return back()->with(
            'success',
            'Password berhasil direset.'
        );
    }

    // DELETE USER
    public function destroy(User $user)
    {
        // CEGAH HAPUS ADMIN SENDIRI
        if ($user->isAdmin()) {

            $adminCount = User::where(
                'role',
                'admin'
            )->count();

            if ($adminCount <= 1) {

                return back()->withErrors([

                    'user' =>
                        'Minimal harus ada satu admin.'
                ]);
            }
        }

        if (
            Auth::id() === $user->id
        ) {

            return back()->withErrors([

                'user' =>
                    'Tidak bisa menghapus akun sendiri.'
            ]);
        }

        // VR-08-3: CEGAH HAPUS SISWA YANG MASIH ADA KEGIATAN
        if (
            $user->isSiswa()
            &&
            $user->kegiatans()->exists()
        ) {

            return back()->withErrors([

                'user' =>
                    'Siswa ini masih memiliki data kegiatan. Nonaktifkan akun saja, jangan dihapus.'
            ]);
        }

        // RESET WALI KELAS JIKA GURU
        if ($user->isGuru()) {

            SchoolClass::where(
                'teacher_id',
                $user->id
            )->update([

                'teacher_id' => null
            ]);
        }

        $user->delete();

        return redirect()->back();
    }
    
    // DAFTAR LAPORAN KESALAHAN DATA
    public function corrections()
    {
        $corrections = DataCorrection::with('user')

            ->latest()

            ->paginate(10);

        return Inertia::render(
            'Admin/Users/Corrections',
            [

                'corrections' =>
                    $corrections,
            ]
        );
    }

    // TANDAI LAPORAN SELESAI
    public function resolveCorrection(
        DataCorrection $correction
    )
    {
        $correction->update([

            'status' => 'selesai',
        ]);

        return redirect()->back();
    }    
} 