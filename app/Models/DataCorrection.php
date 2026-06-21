<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataCorrection extends Model
{
    protected $fillable = [

        'user_id',

        'keterangan',

        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}