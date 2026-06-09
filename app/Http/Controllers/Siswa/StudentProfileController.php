<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentProfileController extends Controller
{
    // HALAMAN PROFIL
    public function index()
    {
        $profile = StudentProfile::with('schoolClass')

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

            'school_class_id' => 'nullable|exists:school_classes,id',

            'birth_date' => 'nullable|date',

            'address' => 'nullable|string|max:1000',

            'parent_name' => 'nullable|string|max:255',

            'parent_phone' => 'nullable|string|max:50',

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

        return redirect()->back();
    }
}