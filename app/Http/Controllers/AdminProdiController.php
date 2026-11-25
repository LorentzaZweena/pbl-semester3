<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProdiController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending'    => DB::table('pengajuan_pkl')->where('status_verifikasi', 'pending')->count(),
            'revisi'     => DB::table('pengajuan_pkl')->where('status_verifikasi', 'revisi')->count(),
            'aktif'      => DB::table('pengajuan_pkl')->where('status_verifikasi', 'aktif_pkl')->count(),
            'sisa_kuota' => DB::table('dosen')->sum(DB::raw('kuota_maksimal - kuota_terpakai')),
        ];

        $antrean = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.nim', '=', 'mahasiswa.nim')
            ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
            ->select(
                'pengajuan_pkl.*',
                'mahasiswa.nama_lengkap as nama_mahasiswa',
                'mahasiswa.prodi',
                'instansi_mitra.nama_perusahaan'
            )
            ->orderByDesc('pengajuan_pkl.created_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'antrean'));
    }

    public function pengajuan(Request $request)
    {
        $query = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.nim', '=', 'mahasiswa.nim')
            ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
            ->leftJoin('dosen as d1', 'pengajuan_pkl.opsi_dosbing_1', '=', 'd1.nip')
            ->leftJoin('dosen as dt', 'pengajuan_pkl.dosbing_terpilih', '=', 'dt.nip')
            ->select(
                'pengajuan_pkl.*',
                'mahasiswa.nama_lengkap as nama_mahasiswa',
                'mahasiswa.prodi',
                'mahasiswa.kelas',
                'instansi_mitra.nama_perusahaan',
                'd1.nama_lengkap as nama_dosbing_1',
                'dt.nama_lengkap as nama_dosbing_terpilih'
            );

        if ($request->filled('status')) {
            $query->where('pengajuan_pkl.status_verifikasi', $request->status);
        }

        $pengajuanList = $query->orderByDesc('pengajuan_pkl.created_at')->get();
        $dosenList = DB::table('dosen')->orderBy('nama_lengkap')->get();

        return view('admin.pengajuan', compact('pengajuanList', 'dosenList'));
    }

    public function verifikasiPengajuan(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:pending,revisi,disetujui_admin,aktif_pkl,ditolak',
            'dosbing_terpilih'  => 'nullable|exists:dosen,nip',
            'catatan_admin'     => 'nullable|string',
        ]);

        $lama = DB::table('pengajuan_pkl')->where('id', $id)->first();

        DB::table('pengajuan_pkl')->where('id', $id)->update([
            'status_verifikasi' => $request->status_verifikasi,
            'dosbing_terpilih'  => $request->dosbing_terpilih,
            'catatan_admin'     => $request->catatan_admin,
            'verified_at'       => now(),
            'updated_at'        => now(),
        ]);

        if ($request->dosbing_terpilih && $lama && $lama->dosbing_terpilih !== $request->dosbing_terpilih) {
            if ($lama->dosbing_terpilih) {
                DB::table('dosen')->where('nip', $lama->dosbing_terpilih)->decrement('kuota_terpakai');
            }
            DB::table('dosen')->where('nip', $request->dosbing_terpilih)->increment('kuota_terpakai');
        }

        return back()->with('success', 'Status pengajuan PKL dan penetapan pembimbing berhasil disimpan.');
    }

    public function dosen()
    {
        $dosenList = DB::table('dosen')->orderBy('nama_lengkap')->get();
        return view('admin.dosen', compact('dosenList'));
    }

    public function updateKuotaDosen(Request $request, $nip)
    {
        $request->validate(['kuota_maksimal' => 'required|integer|min:1|max:30']);

        DB::table('dosen')->where('nip', $nip)->update([
            'kuota_maksimal' => $request->kuota_maksimal,
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Kuota maksimal dosen pembimbing berhasil diperbarui.');
    }

    public function monitoring()
    {
        $dataMonitoring = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.nim', '=', 'mahasiswa.nim')
            ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
            ->leftJoin('penilaian_pkl', 'pengajuan_pkl.id', '=', 'penilaian_pkl.pengajuan_id')
            ->select(
                'mahasiswa.nim',
                'mahasiswa.nama_lengkap',
                'mahasiswa.prodi',
                'instansi_mitra.nama_perusahaan',
                'pengajuan_pkl.status_verifikasi',
                'penilaian_pkl.rata_rata_industri',
                'penilaian_pkl.rata_rata_kampus',
                'penilaian_pkl.nilai_akhir_angka',
                'penilaian_pkl.nilai_akhir_huruf',
                'penilaian_pkl.status_kelulusan'
            )
            ->orderBy('mahasiswa.nim')
            ->get();

        return view('admin.monitoring', compact('dataMonitoring'));
    }
}