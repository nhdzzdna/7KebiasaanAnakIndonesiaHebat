<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {

            $table->id();

            // SISWA
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // TANGGAL LAPORAN
            $table->date('report_date');

            // STATUS
            $table->enum('status', [
                'draft',
                'submitted',
                'evaluated'
            ])->default('draft');

            // SELFIE VALIDASI
            $table->string('selfie_photo')
                ->nullable();

            // WAKTU SUBMIT
            $table->timestamp('submitted_at')
                ->nullable();

            // NILAI GURU
            $table->enum('grade', [
                'A',
                'B',
                'C',
                'D'
            ])->nullable();

            // CATATAN GURU
            $table->text('teacher_note')
                ->nullable();

            // PERSENTASE KEPATUHAN
            $table->integer('compliance_percentage')
                ->default(0);

            // STREAK
            $table->integer('streak')
                ->default(0);

            $table->timestamps();

            // 1 SISWA = 1 LAPORAN/HARI
            $table->unique([
                'student_id',
                'report_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};