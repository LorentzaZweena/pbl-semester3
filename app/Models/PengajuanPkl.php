<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPkl extends Model
{
    protected $table = 'pengajuan_pkl';
    protected $guarded = ['id'];

    public function mahasiswa() { 
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim'); 
    }

    public function instansi() { 
        return $this->belongsTo(InstansiMitra::class, 'instansi_id'); 
    }

    public function pembimbingIndustri() { 
        return $this->belongsTo(PembimbingIndustri::class, 'pembimbing_industri_id'); 
    }
    
    public function opsiDosbing1() { 
        return $this->belongsTo(Dosen::class, 'opsi_dosbing_1', 'nip'); 
    
        }
    public function opsiDosbing2() { 
        return $this->belongsTo(Dosen::class, 'opsi_dosbing_2', 'nip'); 
    }
    public function opsiDosbing3() { 
        return $this->belongsTo(Dosen::class, 'opsi_dosbing_3', 'nip'); 
    }
    public function dosbingTerpilih() { 
        return $this->belongsTo(Dosen::class, 'dosbing_terpilih', 'nip'); 
    }

    public function userRequirements() { 
        return $this->hasMany(UserRequirement::class, 'pengajuan_id'); 
    }

    public function logbookHarian() { 
        return $this->hasMany(LogbookHarian::class, 'pengajuan_id'); 
        }
    public function logbookBimbingan() { 
        return $this->hasMany(LogbookBimbingan::class, 'pengajuan_id'); 
    }

    public function penilaian() { 
        return $this->hasOne(PenilaianPkl::class, 'pengajuan_id'); 
    }
    public function dokumenAkhir() { 
        return $this->hasOne(DokumenAkhir::class, 'pengajuan_id'); 
    }
}
