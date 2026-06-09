<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Kegiatan;
use App\Models\SchoolClass;

use Illuminate\Http\Request;

use Inertia\Inertia;

class MonitoringController extends Controller
{
    // MONITORING GLOBAL
    public function index(Request $request)
    {
        $query = Kegiatan::with([
            'user.studentProfile.schoolClass'
        ]);

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

        // FILTER TANGGAL
        if ($request->filled('tanggal')) {

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );
        }

        // FILTER KELAS
        if ($request->filled('school_class_id')) {

            $query->whereHas(
                'user.studentProfile',
                function ($q) use ($request) {

                    $q->where(
                        'school_class_id',
                        $request->school_class_id
                    );
                }
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

        $kegiatans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // DATA KELAS
        $classes = SchoolClass::get([
            'id',
            'name'
        ]);

        return Inertia::render(
            'Admin/Monitoring/Index',
            [

                'kegiatans' =>
                    $kegiatans,

                'classes' =>
                    $classes,

                'filters' => [

                    'status' =>
                        $request->status,

                    'nilai_guru' =>
                        $request->nilai_guru,

                    'tanggal' =>
                        $request->tanggal,

                    'school_class_id' =>
                        $request->school_class_id,

                    'search' =>
                        $request->search,
                ]
            ]
        );
    }

    // DETAIL MONITORING
    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load([
            'user.studentProfile.schoolClass'
        ]);

        return Inertia::render(
            'Admin/Monitoring/Show',
            [
                'kegiatan' => $kegiatan
            ]
        );
    }
}