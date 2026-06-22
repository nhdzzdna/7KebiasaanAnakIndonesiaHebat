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

        'keterangan_bangun',
        'keterangan_tidur',
        'bukti_foto_bangun',
        'bukti_foto_ibadah',
        'bukti_foto_makan',
        'bukti_foto_olahraga',
        'bukti_foto_belajar',
        'bukti_foto_sosial',
    ];

    protected $casts = [

        'tanggal' => 'date',

        'submitted_at' => 'datetime',

        'detail_ibadah_centang' => 'array',
    ];

    protected $appends = [

        'habits_filled_count',
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

    // JUMLAH KEBIASAAN YANG TERISI (X DARI 7)
    public function getHabitsFilledCountAttribute()
    {
        $habitFields = [
            'waktu_bangun',
            'detail_ibadah_centang',
            'menu_makan',
            'jenis_olahraga',
            'belajar_mandiri',
            'aktivitas_sosial',
            'waktu_tidur',
        ];

        return collect($habitFields)
            ->filter(fn ($field) =>
                !empty($this->$field)
            )

            ->count();
    }
}
