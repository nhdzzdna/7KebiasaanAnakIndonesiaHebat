<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kegiatan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h2 { margin-bottom: 4px; }
        p.periode { margin-top: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; }
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
                <th class="text-center">Hari</th>
                <th class="text-center">Bangun</th>
                <th class="text-center">Olahraga</th>
                <th class="text-center">Makan</th>
                <th class="text-center">Belajar</th>
                <th class="text-center">Ibadah</th>
                <th class="text-center">Tidur</th>
                <th class="text-center">Sosial</th>
                <th class="text-center">Rata-rata</th>
                <th class="text-center">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['kelas'] }}</td>
                    <td class="text-center">{{ $row['total_hari'] }}</td>
                    <td class="text-center">{{ $row['bangun'] }}%</td>
                    <td class="text-center">{{ $row['olahraga'] }}%</td>
                    <td class="text-center">{{ $row['makan'] }}%</td>
                    <td class="text-center">{{ $row['belajar'] }}%</td>
                    <td class="text-center">{{ $row['ibadah'] }}%</td>
                    <td class="text-center">{{ $row['tidur'] }}%</td>
                    <td class="text-center">{{ $row['sosial'] }}%</td>
                    <td class="text-center">{{ $row['rata_rata_kepatuhan'] }}%</td>
                    <td class="text-center">{{ $row['nilai_akhir'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Belum ada data untuk periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>