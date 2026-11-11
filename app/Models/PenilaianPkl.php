<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPkl extends Model
{
    protected $table = 'penilaian_pkl';
    protected $guarded = ['id'];

    public function pengajuanPkl()
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }
}
