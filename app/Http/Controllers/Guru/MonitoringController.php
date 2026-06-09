<?php

namespace App\Http\Controllers\Guru;


use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ])

        ->whereIn('status', [
            'submitted',
            'evaluated'
        ]);

        // FILTER BERDASARKAN KELAS GURU
        $teacherClassId = \Illuminate\Support\Facades\Auth::user()
            ?->teacherProfile
            ?->school_class_id;

        if ($teacherClassId) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($teacherClassId) {

                    $q->where(
                        'school_class_id',
                        $teacherClassId
                    );
                }
            );
        }

        // FILTER TANGGAL
        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
        }

        // FILTER NILAI
        if ($request->filled('nilai_guru')) {

            $query->where(
                'nilai_guru',
                $request->nilai_guru
            );
        }

        // SEARCH SISWA
        if ($request->filled('search')) {

            $query->whereHas(
                'user',
                function ($q) use ($request) {

                    $q->where(
                        'name',
                        'like',
                        '%' . $request->search . '%'
                    );
                }
            );
        }

        // SORT COMPLIANCE
        $query->orderByDesc(
            'compliance_percentage'
        );

        $kegiatans = $query
            ->paginate(10)
            ->withQueryString();

        return Inertia::render(
            'Guru/Monitoring/Index',
            [

                'kegiatans' => $kegiatans,

                'filters' => [

                    'tanggal' =>
                        $request->tanggal,

                    'nilai_guru' =>
                        $request->nilai_guru,

                    'search' =>
                        $request->search,
                ]
            ]
        );
    }
}