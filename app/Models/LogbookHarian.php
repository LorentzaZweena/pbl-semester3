<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookHarian extends Model
{
    protected $table = 'logbook_harian';
    protected $guarded = ['id'];

    public function pengajuanPkl()
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }
}
