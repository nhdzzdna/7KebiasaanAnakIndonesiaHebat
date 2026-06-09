<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {

            $table->id();

            // USER GURU
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // KELAS YANG DIAMPU
            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            // DATA GURU
            $table->string('nip')
                ->nullable();

            $table->string('phone')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};