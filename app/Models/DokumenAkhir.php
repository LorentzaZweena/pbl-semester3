<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenAkhir extends Model
{
    protected $table = 'dokumen_akhir';
    protected $guarded = ['id'];

    public function pengajuanPkl()
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }
}
