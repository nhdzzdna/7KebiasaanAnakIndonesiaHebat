<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [

        'student_id',

        'report_date',

        'status',

        'selfie_photo',

        'submitted_at',

        'grade',

        'teacher_note',

        'compliance_percentage',

        'streak',
    ];

    protected $casts = [

        'report_date' => 'date',

        'submitted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

  //  public function habits()
  //  {
      //  return $this->hasMany(HabitRecord::class);
    //}
}