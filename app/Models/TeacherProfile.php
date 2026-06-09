<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [

        'user_id',

        'school_class_id',

        'nip',

        'phone',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    // USER GURU
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    // KELAS YANG DIAJAR
    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'school_class_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    // SUDAH PUNYA KELAS?
    public function hasClass()
    {
        return !is_null(
            $this->school_class_id
        );
    }

    // PROFILE LENGKAP?
    public function isProfileComplete()
    {
        return
            !empty($this->nip)
            &&
            !empty($this->phone);
    }
}