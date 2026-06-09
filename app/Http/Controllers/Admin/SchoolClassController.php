<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SchoolClass;
use App\Models\TeacherProfile;
use App\Models\User;

use Illuminate\Http\Request;

use Inertia\Inertia;

class SchoolClassController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST KELAS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $classes = SchoolClass::with([
            'teacher',
            'students.user'
        ])

        ->latest()

        ->get()

        ->map(function ($class) {

            return [

                'id' =>
                    $class->id,

                'name' =>
                    $class->name,

                'school_year' =>
                    $class->school_year,

                'teacher' =>
                    $class->teacher?->name,

                'teacher_id' =>
                    $class->teacher_id,

                'total_students' =>
                    $class->students->count(),
            ];
        });

        // LIST GURU AKTIF
        $teachers = User::where(
            'role',
            'guru'
        )

        ->where(
            'status_akun',
            'aktif'
        )

        ->get([
            'id',
            'name'
        ]);

        return Inertia::render(
            'Admin/SchoolClasses/Index',
            [

                'classes' =>
                    $classes,

                'teachers' =>
                    $teachers,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH KELAS
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' =>
                'required|string|max:255|unique:school_classes,name',

            'teacher_id' =>
                'nullable|exists:users,id',

            'school_year' =>
                'required|string|max:255',
        ]);

        // VALIDASI ROLE GURU
        if (!empty($validated['teacher_id'])) {

            $teacher = User::find(
                $validated['teacher_id']
            );

            if (!$teacher?->isGuru()) {

                return back()->withErrors([

                    'teacher_id' =>
                        'User bukan guru.'
                ]);
            }

            // CEK SUDAH JADI WALI KELAS?
            $existingClass = SchoolClass::where(
                'teacher_id',
                $teacher->id
            )->first();

            if ($existingClass) {

                return back()->withErrors([

                    'teacher_id' =>
                        'Guru sudah menjadi wali kelas lain.'
                ]);
            }
        }

        $class = SchoolClass::create($validated);

        // SYNC TEACHER PROFILE
        if (!empty($validated['teacher_id'])) {

            TeacherProfile::updateOrCreate(
                [
                    'user_id' =>
                        $validated['teacher_id']
                ],
                [
                    'school_class_id' =>
                        $class->id
                ]
            );
        }

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE KELAS
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        SchoolClass $schoolClass
    ) {

        $validated = $request->validate([

            'name' =>
                'required|string|max:255|unique:school_classes,name,' . $schoolClass->id,

            'teacher_id' =>
                'nullable|exists:users,id',

            'school_year' =>
                'required|string|max:255',
        ]);

        // VALIDASI ROLE GURU
        if (!empty($validated['teacher_id'])) {

            $teacher = User::find(
                $validated['teacher_id']
            );

            if (!$teacher?->isGuru()) {

                return back()->withErrors([

                    'teacher_id' =>
                        'User bukan guru.'
                ]);
            }

            // CEK WALI KELAS GANDA
            $existingClass = SchoolClass::where(
                'teacher_id',
                $teacher->id
            )

            ->where(
                'id',
                '!=',
                $schoolClass->id
            )

            ->first();

            if ($existingClass) {

                return back()->withErrors([

                    'teacher_id' =>
                        'Guru sudah menjadi wali kelas lain.'
                ]);
            }
        }

        $schoolClass->update($validated);

        // SYNC TEACHER PROFILE
        if (!empty($validated['teacher_id'])) {

            TeacherProfile::updateOrCreate(
                [
                    'user_id' =>
                        $validated['teacher_id']
                ],
                [
                    'school_class_id' =>
                        $schoolClass->id
                ]
            );
        }

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE KELAS
    |--------------------------------------------------------------------------
    */

    public function destroy(
        SchoolClass $schoolClass
    ) {

        // RESET TEACHER PROFILE
        TeacherProfile::where(
            'school_class_id',
            $schoolClass->id
        )->update([

            'school_class_id' => null
        ]);

        $schoolClass->delete();

        return redirect()->back();
    }
}