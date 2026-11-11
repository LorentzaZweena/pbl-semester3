<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstansiMitra extends Model
{
    protected $table = 'instansi_mitra';
    protected $fillable = ['nama_perusahaan', 'alamat', 'bidang_industri', 'kontak_hrd'];

    public function pembimbingIndustri() { 
        return $this->hasMany(PembimbingIndustri::class, 'instansi_id'); 
    }
    public function pengajuanPkl() { 
        return $this->hasMany(PengajuanPkl::class, 'instansi_id'); 
    }
}
