<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PengajuanPklController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $dosenList = DB::table('dosen')->orderBy('nama_lengkap')->get();
        $instansiList = DB::table('instansi_mitra')->orderBy('nama_perusahaan')->get();

        $pengajuanAktif = null;
        if ($mahasiswa) {
            $pengajuanAktif = DB::table('pengajuan_pkl')
                ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
                ->select('pengajuan_pkl.*', 'instansi_mitra.nama_perusahaan', 'instansi_mitra.bidang_industri', 'instansi_mitra.alamat')
                ->where('pengajuan_pkl.nim', $mahasiswa->nim)
                ->latest('pengajuan_pkl.created_at')
                ->first();
        }

        return view('mahasiswa.pengajuan', compact('mahasiswa', 'dosenList', 'instansiList', 'pengajuanAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan'       => 'required|string|max:255',
            'bidang_industri'       => 'required|string|max:255',
            'alamat_perusahaan'     => 'required|string',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after:tanggal_mulai',
            'file_bukti_keterimaan' => 'required|mimes:pdf|max:2048',
            'opsi_dosbing_1'        => 'required|exists:dosen,nip',
            'opsi_dosbing_2'        => 'required|exists:dosen,nip|different:opsi_dosbing_1',
            'opsi_dosbing_3'        => 'required|exists:dosen,nip|different:opsi_dosbing_1|different:opsi_dosbing_2',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;

        $instansi = DB::table('instansi_mitra')
            ->where('nama_perusahaan', $request->nama_perusahaan)
            ->first();

        if (!$instansi) {
            $instansiId = DB::table('instansi_mitra')->insertGetId([
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat'          => $request->alamat_perusahaan,
                'bidang_industri' => $request->bidang_industri,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } else {
            $instansiId = $instansi->id;
        }

        $filePath = $request->file('file_bukti_keterimaan')->store('bukti_keterimaan', 'public');
        DB::table('pengajuan_pkl')->insert([
            'nim'                   => $mahasiswa->nim,
            'instansi_id'           => $instansiId,
            'opsi_dosbing_1'        => $request->opsi_dosbing_1,
            'opsi_dosbing_2'        => $request->opsi_dosbing_2,
            'opsi_dosbing_3'        => $request->opsi_dosbing_3,
            'file_bukti_keterimaan' => $filePath,
            'tanggal_mulai'         => $request->tanggal_mulai,
            'tanggal_selesai'       => $request->tanggal_selesai,
            'status_verifikasi'     => 'pending',
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return redirect()->route('mahasiswa.pengajuan')->with('success', 'Pengajuan PKL berhasil dikirim dan sedang menunggu verifikasi Admin Prodi.');
    }
}
