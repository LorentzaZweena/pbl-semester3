<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nip', 'user_id', 'nama_lengkap', 'prodi_homebase', 'kuota_maksimal', 'kuota_terpakai'];

    public function user() { return $this->belongsTo(User::class); }
    public function bimbinganPkl() { return $this->hasMany(PengajuanPkl::class, 'dosbing_terpilih', 'nip'); }
}