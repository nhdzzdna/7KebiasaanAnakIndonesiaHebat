<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = [

        'user_id',

        'tanggal',

        'waktu_bangun',

        'detail_ibadah_centang',

        'detail_ibadah_lain',

        'menu_makan',

        'jumlah_air',

        'jenis_olahraga',

        'durasi_olahraga',

        'belajar_mandiri',

        'durasi_belajar',

        'aktivitas_sosial',

        'waktu_tidur',

        'bukti_foto',

        'selfie_validasi',

        'status',

        'submitted_at',

        'compliance_percentage',

        'nilai_guru',

        'catatan_guru',
    ];

    protected $casts = [

        'tanggal' => 'date',

        'submitted_at' => 'datetime',

        'detail_ibadah_centang' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isEvaluated()
    {
        return $this->status === 'evaluated';
    }
}
