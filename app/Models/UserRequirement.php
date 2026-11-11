<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRequirement extends Model
{
    protected $table = 'user_requirements';
    protected $guarded = ['id'];

    public function pengajuanPkl()
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }
}
