<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembimbingIndustri extends Model
{
    protected $table = 'pembimbing_industri';
    protected $fillable = ['user_id', 'instansi_id', 'nama_lengkap', 'jabatan', 'no_hp'];

    public function user() { return $this->belongsTo(User::class); }
    public function instansi() { return $this->belongsTo(InstansiMitra::class, 'instansi_id'); }
    public function pengajuanPkl() { return $this->hasMany(PengajuanPkl::class, 'pembimbing_industri_id'); }
}
