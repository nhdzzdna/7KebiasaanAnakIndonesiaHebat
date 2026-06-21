# Dokumentasi API Backend
Sistem Monitoring "7 Kebiasaan Anak Indonesia Hebat"

Dokumen ini untuk siapa pun yang mengerjakan tampilan (FE) di atas backend Laravel ini. Berisi cara kerja arsitekturnya, lalu daftar lengkap semua endpoint beserta data yang dikirim untuk masing-masing halaman.

---

## 1. Cara Kerja Arsitektur Ini (Inertia.js)

Project ini **bukan** REST API biasa yang diakses lewat `fetch()` atau `axios`. Ini pakai Inertia.js, yang artinya Vue dan Laravel jalan menyatu. Beberapa konsekuensi penting:

### Props otomatis nempel, tidak perlu fetch manual

Setiap route di `routes/web.php` memanggil `Inertia::render('Nama/Halaman', [data])` dari controller. Data itu otomatis sampai ke komponen Vue yang namanya sama, lewat `defineProps()`. Contoh: kalau backend manggil

```php
return Inertia::render('Siswa/Dashboard', [
    'stats' => $stats,
    'todayReport' => $todayReport,
]);
```

maka file `resources/js/Pages/Siswa/Dashboard.vue` otomatis menerima `stats` dan `todayReport` sebagai props, cukup dengan:

```vue
<script setup>
defineProps({
    stats: Object,
    todayReport: Object,
});
</script>
```

Tidak perlu menulis kode untuk mengambil data ini — begitu halaman dibuka, datanya sudah ada.

### Nama komponen Vue harus sama persis dengan nama yang dipanggil controller

Kalau controller manggil `Inertia::render('Guru/Monitoring/Index', ...)`, maka file Vue-nya **harus** ada di `resources/js/Pages/Guru/Monitoring/Index.vue`. Kalau filenya belum ada atau salah nama/folder, Inertia akan error "Page not found". Semua nama komponen yang dipanggil tiap controller, sudah saya tulis di tiap endpoint di bawah.

### Mengirim form pakai `useForm()`, bukan fetch manual

Untuk submit data (POST/PATCH/PUT/DELETE), pakai helper `useForm` dari `@inertiajs/vue3`:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
});

function submit() {
    form.post('/admin/users');
}
</script>
```

**Penting:** nama key di `useForm({...})` harus **sama persis** dengan nama field yang divalidasi di controller (`$request->validate([...])`). Kalau backend minta `nilai_guru`, kirim dengan key `nilai_guru`, bukan `nilai` atau `grade`.

### Error validasi otomatis muncul di `form.errors`

Kalau validasi backend gagal, Laravel kirim balik daftar error per field. Dengan `useForm()`, itu otomatis tersedia di `form.errors.nama_field`:

```vue
<input v-model="form.email" />
<span v-if="form.errors.email">{{ form.errors.email }}</span>
```

### Link pakai `route()`, bukan hardcode URL

Project ini biasanya pakai Ziggy (`route('nama.route')`) supaya link gak perlu hardcode path. Nama route lengkap ada di `routes/web.php` — daftarnya juga saya sertakan di tiap endpoint di bawah ini (ditulis di belakang method, contoh: `name: guru.monitoring.index`).

### Upload file (foto, selfie, bukti kegiatan)

Untuk form yang ada file, `useForm()` otomatis mendeteksi `File` object dan mengirim sebagai `multipart/form-data` — tidak perlu setting tambahan, cukup isi field-nya dengan object file dari `<input type="file">`.

---

## 2. Autentikasi & Role

- Semua route (kecuali `/login`) butuh login (`auth` middleware) dan akun berstatus aktif (`check.account.status` middleware).
- Ada 3 role: `admin`, `guru`, `siswa`. Tiap role punya prefix URL sendiri (`/admin/...`, `/guru/...`, `/siswa/...`) dan dijaga middleware `role:nama_role`. Kalau role gak sesuai, dapat HTTP 403.
- Setelah login, redirect otomatis ke dashboard sesuai role.
- Tidak ada self-register — semua akun dibuat oleh admin.

---

## 3. AUTH

### POST `/login`
Body: `email`, `password`
Redirect ke dashboard sesuai role kalau berhasil. Kalau gagal, error muncul di `form.errors.email`.

### POST `/logout`
Tidak ada body.

---

## 4. SISWA
*(prefix `/siswa`, semua route butuh role `siswa`)*

### GET `/siswa/dashboard` — name: `siswa.dashboard`
Komponen Vue: `Siswa/Dashboard`

Props:
```
stats: {
    total_reports, total_submitted, average_compliance,
    profile_completion, streak,
    habits_filled_today,   // 0-7, berapa kebiasaan terisi HARI INI
    habits_total            // selalu 7
}
todayReport: object | null      // data kegiatan hari ini
todayStatus: string             // "belum_membuat" | "draft" | "submitted" | "evaluated"
isTodaySubmitted: boolean
isTodayEvaluated: boolean
todayCompliance: number
profileWarning: boolean         // true kalau profil belum 100% lengkap
latestEvaluation: object | null
latestFeedback: string | null   // catatan guru terakhir
recentReports: array            // 5 kegiatan terakhir
weeklyChart: [                  // 7 elemen, Senin-Minggu minggu ini
    { hari: "Sen", tanggal: "2026-06-15", compliance: 86 },
    ...
]
```

### GET `/siswa/kegiatan` — name: `siswa.kegiatan.index`
Komponen: `Siswa/Kegiatan/Index`
Props: `kegiatanHariIni` (object | null)

### GET `/siswa/kegiatan/selfie` — name: `siswa.kegiatan.selfie`
Komponen: `Siswa/Kegiatan/Selfie`
Redirect ke `kegiatan.index` kalau belum ada draft hari ini.

### GET `/siswa/kegiatan/success` — name: `siswa.kegiatan.success`
Komponen: `Siswa/Kegiatan/Succes` *(perhatikan nama file: "Succes" tanpa "s" kedua)*

### POST `/siswa/kegiatan` — name: `siswa.kegiatan.store`
Body (form-data karena ada file):
```
waktu_bangun            string, format "HH:mm", opsional
detail_ibadah_centang   array, opsional
detail_ibadah_lain      string, opsional, max 1000
menu_makan               string, opsional, max 255
jumlah_air               integer, opsional
jenis_olahraga           string, opsional, max 255
durasi_olahraga          integer, opsional
belajar_mandiri          string, opsional, max 255
durasi_belajar           integer, opsional
aktivitas_sosial         string, opsional, max 1000
waktu_tidur              string, format "HH:mm", opsional
bukti_foto               file gambar, opsional, max 2MB
selfie_validasi          file gambar, WAJIB jika status="submitted"
status                   "draft" atau "submitted", wajib
```
Error khusus: kalau hari ini sudah submitted/evaluated, error muncul di `form.errors.tanggal` ("Kegiatan hari ini sudah terkirim"). Kalau mengisi ibadah tapi data agama belum diisi di profil, error muncul di `form.errors.religion`.

### GET `/siswa/riwayat` — name: `siswa.riwayat.index`
Komponen: `Siswa/Riwayat/Index`
Query: `tanggal`, `status`, `nilai_guru` (semua opsional, untuk filter)
Props: `kegiatans` (paginated, 10/halaman), `filters`

Catatan: tiap object kegiatan otomatis punya field tambahan `habits_filled_count` (0-7) — ini bukan kolom database, tapi dihitung otomatis tiap kali data kegiatan dikirim ke FE, jadi bisa langsung dipakai tanpa hitung manual di FE.

### GET `/siswa/riwayat/{kegiatan}` — name: `siswa.riwayat.show`
Komponen: `Siswa/Kegiatan/Show`
Props: `kegiatan`
403 kalau bukan milik siswa yang login. 404 kalau masih draft.

### GET `/siswa/profile` — name: `siswa.profile.index`
Komponen: `Siswa/Profile/Index`
Props: `profile` (dengan relasi `schoolClass`, `user`)

Field di dalam `profile`:
```
// DIISI ADMIN (read-only dari sisi siswa, JANGAN buat form edit untuk ini di FE siswa)
birth_date, address, parent_name, parent_phone, school_class_id

// DIISI SISWA SENDIRI (boleh diedit lewat profile.update)
religion, hobby, weight, blood_type, favorite_food,
favorite_subject, favorite_sport, strength, weakness
profile_completion   // 0-100, dihitung otomatis
```

### PATCH `/siswa/profile` — name: `siswa.profile.update`
Body: hanya 9 field "diisi sendiri" di atas, semua opsional. **Jangan kirim** `birth_date`/`address`/`parent_name`/`parent_phone` — backend tidak menerima field itu dari endpoint ini (sengaja, karena itu domain admin).

### POST `/siswa/profile/foto` — name: `siswa.profile.foto`
Body (form-data): `foto` — file gambar, wajib, max 2MB.

### POST `/siswa/profile/report-correction` — name: `siswa.profile.report-correction`
Body: `keterangan` — string, wajib, max 1000. Untuk lapor data yang salah (misal nama orang tua typo) ke admin.

---

## 5. GURU
*(prefix `/guru`, semua route butuh role `guru`)*

### GET `/guru/dashboard` — name: `guru.dashboard`
Komponen: `Guru/Dashboard`

Props:
```
stats: { total_students, today_reports, average_compliance, pending_evaluations, submission_rate }
topPerformers: array        // top 5 berdasarkan compliance_percentage tertinggi
needAttention: array        // siswa dengan compliance < 60%
notSubmitted: array         // siswa yang belum lapor hari ini
latestEvaluations: array    // 5 evaluasi terakhir yang diberikan guru
topActive: array            // top 5 berdasarkan JUMLAH HARI SUBMIT (beda dari topPerformers!)
classWeeklyProgress: [      // 7 elemen, 1 per kebiasaan, progres kelas minggu ini
    { field: "waktu_bangun", label: "Bangun Pagi", persentase: 92 },
    ...
]
classMonthlyChart: [        // 30 elemen, 1 per hari, 30 hari terakhir
    { tanggal: "2026-05-22", rata_rata_kepatuhan: 78 },
    ...
]
```
Kalau guru belum ditugaskan ke kelas manapun, semua array di atas dikirim kosong `[]` dan semua angka di `stats` adalah 0 — bukan error.

### GET `/guru/monitoring` — name: `guru.monitoring.index`
Komponen: `Guru/Monitoring/Index`

Endpoint ini punya **3 mode berbeda**, dipilih lewat query param `mode`. Bentuk response-nya beda total tiap mode — FE harus cek `mode` dulu sebelum render, karena field lain (`kegiatans` vs `perKebiasaan` vs `perTanggal`) cuma ada salah satu sesuai mode aktif.

**mode=per_siswa** (default, kalau `mode` tidak dikirim)
Query opsional: `tanggal`, `nilai_guru`, `search`
```
mode: "per_siswa"
kegiatans: { data: [...], current_page, last_page, ... }   // paginated, 10/halaman
filters: { tanggal, nilai_guru, search }
```

**mode=per_kebiasaan**
Query: `tanggal` (opsional, default hari ini)
```
mode: "per_kebiasaan"
perKebiasaan: [   // selalu 7 elemen, 1 per kebiasaan
    {
        field: "waktu_bangun",
        label: "Bangun Pagi",
        total_terisi: 18,
        total_siswa: 32,
        siswa: [
            { id_kegiatan, nama_siswa, terisi: true, nilai: "04:45" },
            ...
        ]
    },
    ...
]
filters: { tanggal }
```

**mode=kalender**
Query: `bulan` (1-12, opsional, default bulan ini), `tahun` (opsional, default tahun ini)
```
mode: "kalender"
perTanggal: [
    { tanggal: "2026-06-01", total_siswa_lapor: 28, rata_rata_kepatuhan: 84 },
    ...   // hanya tanggal yang ADA datanya, bukan semua tanggal di bulan itu
]
filters: { bulan, tahun }
```

Semua mode otomatis terfilter ke kelas yang diampu guru yang login — tidak perlu kirim parameter kelas dari FE untuk role guru.

### GET `/guru/monitoring/{kegiatan}` — name: `guru.monitoring.show`
Komponen: `Guru/Monitoring/Show`

Props:
```
kegiatan: object   // detail 1 laporan, lengkap dengan relasi user.studentProfile.schoolClass
siswaSummary: {
    compliance_bulan_ini: 98,
    streak: 14,
    total_hari_bulan_ini: 28,
    status_akun: "aktif"
}
chart12Hari: [   // 12 elemen berurutan, hari ini mundur 11 hari
    { tanggal: "2026-06-10", compliance: 86 },
    ...
]
```
403 kalau siswa tersebut bukan dari kelas yang diampu guru ini.

### PATCH `/guru/monitoring/{kegiatan}/evaluasi` — name: `guru.monitoring.evaluasi`
Body:
```
nilai_guru: wajib, salah satu dari "A", "B", "C", "D" (predikat huruf, BUKAN angka 0-100)
catatan_guru: opsional, string
```
Error kalau laporan masih draft, atau kalau sudah pernah dievaluasi sebelumnya (tidak bisa dievaluasi 2x).

### GET `/guru/rekap` — name: `guru.rekap.index`
Komponen: `Guru/Rekap/Index`
Query: `bulan`, `tahun` (opsional, default bulan/tahun ini)

Props:
```
rekapSiswa: [   // 1 baris per siswa
    {
        id_siswa, nama, total_hari,
        rata_rata_kepatuhan, nilai_akhir,   // "A"/"B"/"C"/"D"/"-"
        bangun, olahraga, makan, belajar,    // masing-masing 0-100,
        ibadah, tidur, sosial                // persentase per kebiasaan utk siswa ini
    },
    ...
]
stats: { rata_rata_kelas, siswa_berprestasi, siswa_perlu_bimbingan, sudah_dievaluasi, belum_dievaluasi }
kebiasaanTerlemah: [   // 7 elemen, diurut dari yang TERLEMAH (persentase terkecil dulu)
    { kebiasaan: "Tidur Cepat", persentase: 65 },
    ...
]
trenKepatuhan: [   // 7 elemen, 7 bulan terakhir termasuk bulan ini
    { bulan: "Jun 2026", rata_rata: 84 },
    ...
]
filters: { bulan, tahun }
```

### GET `/guru/rekap/pdf` — name: `guru.rekap.pdf`
Bukan Inertia — ini download file langsung. Query: `bulan`, `tahun`. Hubungkan lewat `<a href="...">` biasa atau `window.location`, jangan pakai `useForm()`/Inertia visit.

### GET `/guru/rekap/excel` — name: `guru.rekap.excel`
Sama seperti PDF, download langsung, query sama.

### GET `/guru/rekap/grafik` — name: `guru.rekap.grafik`
Ini **bukan** Inertia, tapi JSON response biasa — kalau mau dipakai, fetch manual pakai `axios`/`fetch()`, bukan lewat props otomatis. Query: `bulan`, `tahun`. Return: `{ perTanggal: [{ tanggal, rata_rata }] }`.

### GET `/guru/profile` — name: `guru.profile.index`
Komponen: `Guru/Profile/Index`
Props: `user` (dengan relasi `teacherProfile.schoolClass`)

### PATCH `/guru/profile` — name: `guru.profile.update`
Body: `name`, `email` (kelas yang diampu tidak bisa diubah lewat sini — itu diatur dari sisi Admin).

### PATCH `/guru/profile/password` — name: `guru.profile.password`
Body: `current_password` (wajib, harus cocok password saat ini), `password` (wajib, min 8, harus disertai `password_confirmation` yang sama karena ada rule `confirmed`).

### POST `/guru/profile/foto` — name: `guru.profile.foto`
Body (form-data): `foto`.

---

## 6. ADMIN
*(prefix `/admin`, semua route butuh role `admin`)*

### GET `/admin/dashboard` — name: `admin.dashboard`
Komponen: `Admin/Dashboard`

Props:
```
stats: {
    total_users, total_guru, total_siswa, total_classes,
    active_users, inactive_users,
    total_reports, today_reports, draft_reports, submitted_reports, evaluated_reports,
    average_compliance,
    total_admin,
    persen_guru_aktif,             // untuk progress bar "Guru Aktif"
    persen_siswa_aktif,            // untuk progress bar "Siswa Aktif"
    persen_kegiatan_tervalidasi    // untuk progress bar "Kegiatan Tervalidasi"
}
recentReports: array      // 5 kegiatan terbaru
activityChart: [          // 7 elemen, 7 hari terakhir
    { tanggal: "2026-06-15", hari: "Sen", total_aktivitas: 142 },
    ...
]
recentActivity: [         // 5 elemen, log sederhana akun-akun terbaru dibuat
    { description: "Akun siswa Budi Santoso berhasil dibuat", waktu: "5 menit yang lalu" },
    ...
]
topStudents: array        // top 5 siswa berdasarkan compliance
```
Catatan: `recentActivity` cuma mencatat pembuatan akun (bukan log lengkap login/reset password/dll) — itu keterbatasan yang disengaja, lihat catatan di bagian bawah dokumen ini.

### GET `/admin/users` — name: `admin.users.index`
Komponen: `Admin/Users/Index`
Query: `search`, `role` (`admin`/`guru`/`siswa`), `status_akun` (`aktif`/`nonaktif`) — semua opsional

Props:
```
users: { data: [...], current_page, ... }   // paginated, 10/halaman
classes: array           // semua kelas, untuk dropdown
roleCounts: { semua, guru, siswa }   // untuk label tab "Semua (336)", "Guru (24)", dst — total keseluruhan, tidak terpengaruh paginasi
filters: { search, role, status_akun }
```

### POST `/admin/users` — name: `admin.users.store`
Body:
```
name           wajib
email          wajib, harus unik
password       wajib, min 8
role           wajib, "admin" | "guru" | "siswa"
school_class_id   opsional (dipakai kalau role guru/siswa)

// HANYA RELEVAN KALAU role = "siswa":
birth_date, address, parent_name, parent_phone   // semua opsional
```

### PUT `/admin/users/{user}` — name: `admin.users.update`
Body: sama seperti store, tapi tanpa `password` (ganti password punya endpoint sendiri), ditambah `status_akun` (wajib, "aktif"/"nonaktif").
Error khusus: gak bisa ubah role admin terakhir jadi bukan admin, gak bisa nonaktifkan akun sendiri.

### PATCH `/admin/users/{user}/reset-password` — name: `admin.users.reset-password`
Tidak ada body. Reset ke password default `"password123"`.

### DELETE `/admin/users/{user}` — name: `admin.users.destroy`
Tidak ada body.
Error khusus: gak bisa hapus admin terakhir, gak bisa hapus diri sendiri, **gak bisa hapus siswa yang masih punya data kegiatan** (harus nonaktifkan saja).

### GET `/admin/users/corrections` — name: `admin.users.corrections`
Komponen: `Admin/Users/Corrections`
Props: `corrections` (paginated) — daftar laporan kesalahan data dari siswa, dengan relasi `user`.

### PATCH `/admin/users/corrections/{correction}/resolve` — name: `admin.users.corrections.resolve`
Tidak ada body. Tandai 1 laporan jadi status `"selesai"`.

### GET `/admin/classes` — name: `admin.classes.index`
Komponen: `Admin/SchoolClasses/Index`
Props: `classes` (dengan `total_students`, `teacher`), `teachers` (guru aktif yang belum punya kelas)

*(Catatan: halaman ini belum ada di mockup yang kita punya — kalau memang gak dipakai di rencana final FE, endpoint ini boleh diabaikan.)*

### POST `/admin/classes` — name: `admin.classes.store`
Body: `name` (wajib, unik), `teacher_id` (opsional), `school_year` (wajib)

### PUT `/admin/classes/{schoolClass}` — name: `admin.classes.update`
Body: sama seperti store.

### DELETE `/admin/classes/{schoolClass}` — name: `admin.classes.destroy`
Error kalau kelas masih punya siswa.

### GET `/admin/monitoring` — name: `admin.monitoring.index`
Komponen: `Admin/Monitoring/Index`

Sama strukturnya dengan `/guru/monitoring` (3 mode: `per_siswa`/`per_kebiasaan`/`kalender`), bedanya:
- Tidak otomatis terfilter ke 1 kelas (admin lihat semua kelas)
- Bisa kirim query tambahan `school_class_id` untuk filter manual ke kelas tertentu (opsional, di semua mode)
- mode `per_siswa` juga mengirim `classes`, `stats`, `topStudents`, `needGuidance` (lihat detail field di kode kalau perlu, strukturnya mirip dashboard)

### GET `/admin/monitoring/{kegiatan}` — name: `admin.monitoring.show`
Komponen: `Admin/Monitoring/Show`
Props: `kegiatan` (dengan relasi lengkap)

### GET `/admin/profile` — name: `admin.profile.index`
Komponen: `Admin/Profile/Index`
Props:
```
user: object
settings: {
    notifikasi_email: boolean,
    laporan_otomatis: boolean,
    alert_pengguna_baru: boolean
}
```

### PATCH `/admin/profile` — name: `admin.profile.update`
Body: `name`, `email`.

### PATCH `/admin/profile/password` — name: `admin.profile.password`
Body: `current_password`, `password` (+ `password_confirmation`).

### POST `/admin/profile/foto` — name: `admin.profile.foto`
Body (form-data): `foto`.

### PATCH `/admin/profile/settings` — name: `admin.profile.settings`
Body: `notifikasi_email`, `laporan_otomatis`, `alert_pengguna_baru` — ketiganya wajib, boolean.

Catatan jujur: toggle ini cuma menyimpan preferensi ke database. Belum ada sistem terjadwal yang benar-benar mengirim email berdasarkan toggle ini — itu di luar scope yang sudah dikerjakan.

### DELETE `/admin/profile/reset-kegiatan` — name: `admin.profile.reset-kegiatan`
Body: `password` (wajib, harus cocok password admin yang login), `tanggal_mulai` (wajib), `tanggal_akhir` (wajib, harus >= tanggal_mulai).

PERHATIAN — operasi destruktif: menghapus permanen semua data kegiatan dalam rentang tanggal yang dipilih. FE wajib pakai dialog konfirmasi yang jelas sebelum mengirim request ini.

### PATCH `/admin/profile/deactivate-students` — name: `admin.profile.deactivate-students`
Body: `password` (wajib).

PERHATIAN: menonaktifkan SEMUA akun siswa sekaligus (bukan hapus data, cuma `status_akun` jadi `nonaktif`). FE wajib pakai dialog konfirmasi.

---

## 7. Ringkasan Aturan Penting

1. **`nilai_guru` selalu string `"A"`/`"B"`/`"C"`/`"D"`** di seluruh sistem — jangan pernah kirim atau harapkan angka.
2. **Tanggal selalu format `"YYYY-MM-DD"`** (contoh `"2026-06-21"`).
3. **File upload pakai `multipart/form-data`** — otomatis kalau pakai `useForm()` dan isi field dengan `File` object. Path hasil upload disimpan relatif (perlu prefix `/storage/` di `<img src>` untuk menampilkannya).
4. **`/guru/monitoring` dan `/admin/monitoring` punya 3 bentuk response berbeda** tergantung query `mode` — selalu cek `mode` dulu sebelum mengakses field lain.
5. **Endpoint dengan tanda PERHATIAN adalah operasi sensitif/destruktif** — pastikan FE menambahkan konfirmasi sebelum mengirim.
6. **3 endpoint ini BUKAN Inertia** (perlu fetch manual atau `<a>` biasa, bukan props otomatis): `/guru/rekap/pdf`, `/guru/rekap/excel` (download file), `/guru/rekap/grafik` (JSON biasa).
