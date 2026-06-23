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
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status_akun')) {
            $query->where('status_akun', $request->status_akun);
        }

        // Load relasi profil supaya data lengkap tersedia di Vue
        // Tanpa with(), user.student_profile dan user.teacher_profile akan null
        $users = $query
            ->with(['studentProfile', 'teacherProfile'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $allClasses = SchoolClass::get(['id', 'name']);

        $availableClasses = SchoolClass::whereNull('teacher_id')->get(['id', 'name']);

        $totalSemua = User::count();
        $totalGuru  = User::where('role', 'guru')->count();
        $totalSiswa = User::where('role', 'siswa')->count();

        return Inertia::render('Admin/Users/Index', [
            'users'            => $users,
            'classes'          => $allClasses,
            'availableClasses' => $availableClasses,
            'roleCounts'       => [
                'semua' => $totalSemua,
                'guru'  => $totalGuru,
                'siswa' => $totalSiswa,
            ],
            'filters' => [
                'search'      => $request->search,
                'role'        => $request->role,
                'status_akun' => $request->status_akun,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:8',
            'role'            => 'required|in:admin,guru,siswa',
            'status_akun'     => 'required|in:aktif,nonaktif',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'birth_date'      => 'nullable|date',
            'address'         => 'nullable|string|max:1000',
            'parent_name'     => 'nullable|string|max:255',
            'parent_phone'    => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'status_akun' => $validated['status_akun'],
        ]);

        if ($validated['role'] === 'siswa') {
            StudentProfile::create([
                'user_id'         => $user->id,
                'school_class_id' => $validated['school_class_id'] ?? null,
                'birth_date'      => $validated['birth_date']      ?? null,
                'address'         => $validated['address']         ?? null,
                'parent_name'     => $validated['parent_name']     ?? null,
                'parent_phone'    => $validated['parent_phone']    ?? null,
            ]);
        }

        if ($validated['role'] === 'guru') {
            TeacherProfile::create([
                'user_id'         => $user->id,
                'school_class_id' => $validated['school_class_id'] ?? null,
            ]);

            if ($validated['school_class_id']) {
                SchoolClass::where('id', $validated['school_class_id'])
                    ->update(['teacher_id' => $user->id]);
            }
        }

        // Redirect ke route index supaya Inertia dapat halaman fresh
        return redirect()->route('admin.users.index');
    }

    public function update(Request $request, User $user)
    {
        $oldRole = $user->role;

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            // Nullable supaya password tidak wajib diisi saat edit
            'password'        => 'nullable|min:8',
            'role'            => 'required|in:admin,guru,siswa',
            'status_akun'     => 'required|in:aktif,nonaktif',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'birth_date'      => 'nullable|date',
            'address'         => 'nullable|string|max:1000',
            'parent_name'     => 'nullable|string|max:255',
            'parent_phone'    => 'nullable|string|max:50',
        ]);

        if ($user->isAdmin() && $validated['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['user' => 'Minimal harus ada satu admin.']);
            }
        }

        if (Auth::id() === $user->id && $validated['status_akun'] === 'nonaktif') {
            return back()->withErrors(['user' => 'Tidak bisa menonaktifkan akun sendiri.']);
        }

        // Siapkan data update, password hanya dimasukkan kalau diisi
        $updateData = [
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'role'        => $validated['role'],
            'status_akun' => $validated['status_akun'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Cleanup jika role berubah dari siswa
        if ($oldRole === 'siswa' && $validated['role'] !== 'siswa') {
            $user->studentProfile?->delete();
        }

        // Cleanup jika role berubah dari guru
        if ($oldRole === 'guru' && $validated['role'] !== 'guru') {
            $user->teacherProfile?->delete();
            SchoolClass::where('teacher_id', $user->id)->update(['teacher_id' => null]);
        }

        if ($validated['role'] === 'siswa') {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_class_id' => $validated['school_class_id'] ?? null,
                    'birth_date'      => $validated['birth_date']      ?? null,
                    'address'         => $validated['address']         ?? null,
                    'parent_name'     => $validated['parent_name']     ?? null,
                    'parent_phone'    => $validated['parent_phone']    ?? null,
                ]
            );
        }

        if ($validated['role'] === 'guru') {
            // Lepas kelas lama dulu sebelum assign kelas baru
            SchoolClass::where('teacher_id', $user->id)->update(['teacher_id' => null]);

            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['school_class_id' => $validated['school_class_id'] ?? null]
            );

            if ($validated['school_class_id']) {
                SchoolClass::where('id', $validated['school_class_id'])
                    ->update(['teacher_id' => $user->id]);
            }
        }

        $role   = $request->input('_current_role',   '');
        $search = $request->input('_current_search', '');

        return redirect()->route('admin.users.index', array_filter([
            'role'   => $role,
            'search' => $search,
        ]));
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password123')]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password berhasil direset.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['user' => 'Minimal harus ada satu admin.']);
            }
        }

        if (Auth::id() === $user->id) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun sendiri.']);
        }

        if ($user->isSiswa() && $user->kegiatans()->exists()) {
            return back()->withErrors([
                'user' => 'Siswa ini masih memiliki data kegiatan. Nonaktifkan akun saja.',
            ]);
        }

        if ($user->isGuru()) {
            SchoolClass::where('teacher_id', $user->id)->update(['teacher_id' => null]);
        }

        $user->delete();

        $role   = $request->input('_current_role',   '');
        $search = $request->input('_current_search', '');

        return redirect()->route('admin.users.index', array_filter([
            'role'   => $role,
            'search' => $search,
        ]));
    }

    public function corrections()
    {
        $corrections = DataCorrection::with('user')->latest()->paginate(10);

        return Inertia::render('Admin/Users/Corrections', [
            'corrections' => $corrections,
        ]);
    }

    public function resolveCorrection(DataCorrection $correction)
    {
        $correction->update(['status' => 'selesai']);

        return redirect()->back();
    }
}