<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanNilaiController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuanAktif = null;
        $penilaian = null;

        if ($mahasiswa) {
            $pengajuanAktif = DB::table('pengajuan_pkl')
                ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
                ->select('pengajuan_pkl.*', 'instansi_mitra.nama_perusahaan')
                ->where('pengajuan_pkl.nim', $mahasiswa->nim)
                ->latest('pengajuan_pkl.created_at')
                ->first();

            if ($pengajuanAktif) {
                $penilaian = DB::table('penilaian_pkl')
                    ->leftJoin('dosen', 'penilaian_pkl.nip', '=', 'dosen.nip')
                    ->leftJoin('pembimbing_industri', 'penilaian_pkl.pembimbing_industri_id', '=', 'pembimbing_industri.id')
                    ->select(
                        'penilaian_pkl.*',
                        'dosen.nama_lengkap as nama_dosen',
                        'pembimbing_industri.nama_lengkap as nama_industri'
                    )
                    ->where('penilaian_pkl.pengajuan_id', $pengajuanAktif->id)
                    ->first();
            }
        }

        return view('mahasiswa.laporan', compact('mahasiswa', 'pengajuanAktif', 'penilaian'));
    }

    public function pratinjau()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuanAktif = null;
        $penilaian = null;

        if ($mahasiswa) {
            $pengajuanAktif = DB::table('pengajuan_pkl')
                ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
                ->select('pengajuan_pkl.*', 'instansi_mitra.nama_perusahaan')
                ->where('pengajuan_pkl.nim', $mahasiswa->nim)
                ->latest('pengajuan_pkl.created_at')
                ->first();

            if ($pengajuanAktif) {
                $penilaian = DB::table('penilaian_pkl')
                    ->leftJoin('dosen', 'penilaian_pkl.nip', '=', 'dosen.nip')
                    ->leftJoin('pembimbing_industri', 'penilaian_pkl.pembimbing_industri_id', '=', 'pembimbing_industri.id')
                    ->select(
                        'penilaian_pkl.*',
                        'dosen.nama_lengkap as nama_dosen',
                        'pembimbing_industri.nama_lengkap as nama_industri'
                    )
                    ->where('penilaian_pkl.pengajuan_id', $pengajuanAktif->id)
                    ->first();
            }
        }

        return view('mahasiswa.preview-dokumen', compact('mahasiswa', 'pengajuanAktif', 'penilaian'));
    }
}