<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {

            $table->id();

            // USER
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // KELAS
            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            // DATA ADMIN
            $table->date('birth_date')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('parent_name')
                ->nullable();

            $table->string('parent_phone')
                ->nullable();

            // DATA PERSONAL SISWA
            $table->string('religion')
                ->nullable();

            $table->string('hobby')
                ->nullable();

            $table->integer('weight')
                ->nullable();

            $table->string('blood_type')
                ->nullable();

            $table->string('favorite_food')
                ->nullable();

            $table->string('favorite_subject')
                ->nullable();

            $table->string('favorite_sport')
                ->nullable();

            $table->text('strength')
                ->nullable();

            $table->text('weakness')
                ->nullable();

            // PROFILE COMPLETION
            $table->integer('profile_completion')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};