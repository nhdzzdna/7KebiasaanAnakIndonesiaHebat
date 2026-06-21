<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_corrections', function (Blueprint $table) {

            $table->id();

            // SISWA YANG MELAPOR
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // ISI LAPORAN
            $table->text('keterangan');

            // STATUS PENANGANAN
            $table->enum('status', [
                'menunggu',
                'selesai',
            ])
                ->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_corrections');
    }
};