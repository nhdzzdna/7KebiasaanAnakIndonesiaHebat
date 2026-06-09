<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {

            $table->id();

            // SISWA
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // TANGGAL
            $table->date('tanggal');

            // 1. BANGUN PAGI
            $table->time('waktu_bangun')
                ->nullable();

            // 2. IBADAH & DOA
            $table->json('detail_ibadah_centang')
                ->nullable();

            $table->text('detail_ibadah_lain')
                ->nullable();

            // 3. MAKAN SEHAT
            $table->string('menu_makan')
                ->nullable();

            $table->integer('jumlah_air')
                ->nullable();

            // 4. OLAHRAGA
            $table->string('jenis_olahraga')
                ->nullable();

            $table->integer('durasi_olahraga')
                ->nullable();

            // 5. BELAJAR MANDIRI
            $table->string('belajar_mandiri')
                ->nullable();

            $table->integer('durasi_belajar')
                ->nullable();

            // 6. AKTIVITAS SOSIAL
            $table->text('aktivitas_sosial')
                ->nullable();

            // 7. TIDUR CEPAT
            $table->time('waktu_tidur')
                ->nullable();

            // FOTO BUKTI
            $table->string('bukti_foto')
                ->nullable();

            // SELFIE VALIDASI
            $table->string('selfie_validasi')
                ->nullable();

            // STATUS
            $table->enum('status', [
                'draft',
                'submitted',
                'evaluated'
            ])->default('draft');

            // WAKTU SUBMIT
            $table->timestamp('submitted_at')
                ->nullable();

            // COMPLIANCE
            $table->integer('compliance_percentage')
                ->default(0);

            // EVALUASI GURU
            $table->enum('nilai_guru', [
                'A',
                'B',
                'C',
                'D'
            ])->nullable();

            $table->text('catatan_guru')
                ->nullable();

            $table->timestamps();

            // 1 SISWA = 1 LAPORAN/HARI
            $table->unique([
                'user_id',
                'tanggal'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
