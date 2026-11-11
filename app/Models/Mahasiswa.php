<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nim', 'user_id', 'nama_lengkap', 'prodi', 'kelas', 'no_hp', 'angkatan'];

    public function user() { return $this->belongsTo(User::class); }
    public function pengajuanPkl() { return $this->hasMany(PengajuanPkl::class, 'nim', 'nim'); }
}
