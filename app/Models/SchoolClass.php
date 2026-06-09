<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [

        'name',

        'teacher_id',

        'school_year',
    ];

    // WALI KELAS
    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id'
        );
    }

    // SISWA DALAM KELAS
    public function students()
    {
        return $this->hasMany(
            StudentProfile::class,
            'school_class_id'
        );
    }

    // TOTAL SISWA
    public function totalStudents()
    {
        return $this->students()->count();
    }
}