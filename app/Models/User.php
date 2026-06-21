<?php

namespace App\Models;

use App\Models\Kegiatan;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\DataCorrection;

use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',
        'role',
        'status_akun',
        'foto',
    ];

    protected $hidden = [

        'password',

        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',
        ];
    }

    // KEGIATAN SISWA
    public function kegiatans()
    {
        return $this->hasMany(
            Kegiatan::class
        );
    }

    // PROFILE SISWA
    public function studentProfile()
    {
        return $this->hasOne(
            StudentProfile::class
        );
    }

    // PROFILE GURU
    public function teacherProfile()
    {
        return $this->hasOne(
            TeacherProfile::class
        );
    }

    // LAPORAN KESALAHAN DATA
    public function dataCorrections()
    {
        return $this->hasMany(
            DataCorrection::class
        );
    }

    // WALI KELAS
    public function homeroomClass()
    {
        return $this->hasOne(
            SchoolClass::class,
            'teacher_id'
        );
    }

    // CHECK ROLE
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    // STATUS AKUN
    public function isAktif()
    {
        return $this->status_akun === 'aktif';
    }
}