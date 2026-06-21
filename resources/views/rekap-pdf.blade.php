<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kegiatan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 4px; }
        p.periode { margin-top: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Rekap Kegiatan Siswa</h2>
    <p class="periode">Periode: {{ $namaBulan }} {{ $tahun }} @if($namaKelas) — Kelas {{ $namaKelas }} @endif</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th class="text-center">Total Hari</th>
                <th class="text-center">Rata-rata Kepatuhan (%)</th>
                <th class="text-center">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['kelas'] }}</td>
                    <td class="text-center">{{ $row['total_hari'] }}</td>
                    <td class="text-center">{{ $row['rata_rata_kepatuhan'] }}</td>
                    <td class="text-center">{{ $row['nilai_akhir'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data untuk periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>