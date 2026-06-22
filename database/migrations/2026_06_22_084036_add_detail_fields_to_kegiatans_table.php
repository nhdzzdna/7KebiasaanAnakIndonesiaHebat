<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {

            $table->string('keterangan_bangun')->nullable()->after('waktu_bangun');
            $table->string('keterangan_tidur')->nullable()->after('waktu_tidur');

            $table->string('bukti_foto_bangun')->nullable()->after('bukti_foto');
            $table->string('bukti_foto_ibadah')->nullable()->after('bukti_foto_bangun');
            $table->string('bukti_foto_makan')->nullable()->after('bukti_foto_ibadah');
            $table->string('bukti_foto_olahraga')->nullable()->after('bukti_foto_makan');
            $table->string('bukti_foto_belajar')->nullable()->after('bukti_foto_olahraga');
            $table->string('bukti_foto_sosial')->nullable()->after('bukti_foto_belajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {

            $table->dropColumn([
                'keterangan_bangun',
                'keterangan_tidur',
                'bukti_foto_bangun',
                'bukti_foto_ibadah',
                'bukti_foto_makan',
                'bukti_foto_olahraga',
                'bukti_foto_belajar',
                'bukti_foto_sosial',
            ]);
        });
    }
};
