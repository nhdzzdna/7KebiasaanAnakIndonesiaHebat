<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [

        'user_id',

        'school_class_id',

        'birth_date',

        'address',

        'parent_name',

        'parent_phone',

        'religion',

        'hobby',

        'weight',

        'blood_type',

        'favorite_food',

        'favorite_subject',

        'favorite_sport',

        'strength',

        'weakness',

        'profile_completion',
    ];

    protected $casts = [

        'birth_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO PROFILE COMPLETION
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saving(function ($profile) {

            $profile->profile_completion =
                $profile->calculateProfileCompletion();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE COMPLETION
    |--------------------------------------------------------------------------
    */

    public function calculateProfileCompletion()
    {
        $fields = [

            'birth_date',

            'address',

            'parent_name',

            'parent_phone',

            'religion',

            'hobby',

            'weight',

            'blood_type',

            'favorite_food',

            'favorite_subject',

            'favorite_sport',

            'strength',

            'weakness',
        ];

        $filled = 0;

        foreach ($fields as $field) {

            if (!empty($this->$field)) {

                $filled++;
            }
        }

        return round(
            ($filled / count($fields)) * 100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    // USER SISWA
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    // KELAS SISWA
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

    // PROFILE LENGKAP?
    public function isProfileComplete()
    {
        return
            $this->profile_completion >= 100;
    }

    // SUDAH PUNYA KELAS?
    public function hasClass()
    {
        return !is_null(
            $this->school_class_id
        );
    }
}