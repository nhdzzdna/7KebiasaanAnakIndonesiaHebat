<?php

namespace App\Http\Controllers\Siswa;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::where('user_id', Auth::user()->id)
            ->latest()
            ->get();

        return Inertia::render('Siswa/Kegiatan/Index', [
            'kegiatans' => $kegiatans
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable',
        ]);

        Kegiatan::create([
            'user_id' => Auth::user()->id,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'status' => 'pending',
        ]);

        return redirect()->back();
    }
}