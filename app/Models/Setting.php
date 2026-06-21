<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [

        'key',

        'value',
    ];

    // AMBIL NILAI SETTING (DENGAN DEFAULT)
    public static function getValue(
        string $key,
        $default = null
    )
    {
        return static::where(
            'key',
            $key
        )

        ->value('value')

            ?? $default;
    }

    // SIMPAN/UPDATE NILAI SETTING
    public static function setValue(
        string $key,
        $value
    )
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}