<?php

namespace App\Exports;

use App\Models\Kegiatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapExport implements FromCollection, WithHeadings, WithMapping
{
    protected int $bulan;
    protected int $tahun;
    protected ?int $schoolClassId;

    public function __construct(int $bulan, int $tahun, ?int $schoolClassId = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->schoolClassId = $schoolClassId;
    }

    public function collection()
    {
        $query = Kegiatan::with('user.studentProfile.schoolClass')

            ->whereMonth('tanggal', $this->bulan)

            ->whereYear('tanggal', $this->tahun)

            ->whereIn('status', ['submitted', 'evaluated']);

        if ($this->schoolClassId) {

            $query->whereHas('user.studentProfile', function ($q) {

                $q->where('school_class_id', $this->schoolClassId);
            });
        }

        $habitFieldsRekap = [
            'waktu_bangun' => 'bangun',
            'jenis_olahraga' => 'olahraga',
            'menu_makan' => 'makan',
            'belajar_mandiri' => 'belajar',
            'detail_ibadah_centang' => 'ibadah',
            'waktu_tidur' => 'tidur',
            'aktivitas_sosial' => 'sosial',
        ];

        return $query->get()
            ->groupBy('user_id')
            ->map(function ($items) use ($habitFieldsRekap) {

                $user = $items->first()->user;

                $totalHari = $items->count();

                $perKebiasaan = collect($habitFieldsRekap)
                    ->mapWithKeys(function ($key, $field) use ($items, $totalHari) {

                        $terisi = $items->filter(
                            fn ($k) => !empty($k->$field)
                        )->count();

                        $persen = $totalHari > 0

                            ? round(
                                ($terisi / $totalHari) * 100
                            )

                            : 0;

                        return [$key => $persen];
                    });

                return [
                    'nama' => $user->name,
                    'kelas' => $user->studentProfile?->schoolClass?->name ?? '-',
                    'total_hari' => $totalHari,
                    'rata_rata_kepatuhan' => round($items->avg('compliance_percentage')),
                    'nilai_akhir' => $items->whereNotNull('nilai_guru')->last()?->nilai_guru ?? '-',
                    'bangun' => $perKebiasaan['bangun'],
                    'olahraga' => $perKebiasaan['olahraga'],
                    'makan' => $perKebiasaan['makan'],
                    'belajar' => $perKebiasaan['belajar'],
                    'ibadah' => $perKebiasaan['ibadah'],
                    'tidur' => $perKebiasaan['tidur'],
                    'sosial' => $perKebiasaan['sosial'],
                ];
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'Total Hari Tercatat',
            'Bangun Pagi (%)',
            'Olahraga (%)',
            'Makan Sehat (%)',
            'Belajar Mandiri (%)',
            'Ibadah & Doa (%)',
            'Tidur Cepat (%)',
            'Aktivitas Sosial (%)',
            'Rata-rata Kepatuhan (%)',
            'Nilai Akhir',
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama'],
            $row['kelas'],
            $row['total_hari'],
            $row['bangun'],
            $row['olahraga'],
            $row['makan'],
            $row['belajar'],
            $row['ibadah'],
            $row['tidur'],
            $row['sosial'],
            $row['rata_rata_kepatuhan'],
            $row['nilai_akhir'],
        ];
    }
}