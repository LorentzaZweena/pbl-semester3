<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilMahasiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $pengajuan = null;

        if ($mahasiswa) {
            $pengajuan = DB::table('pengajuan_pkl')
                ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
                ->leftJoin('dosen', 'pengajuan_pkl.dosbing_terpilih', '=', 'dosen.nip')
                ->select(
                    'pengajuan_pkl.*',
                    'instansi_mitra.nama_perusahaan',
                    'instansi_mitra.bidang_industri',
                    'dosen.nama_lengkap as nama_dosbing'
                )
                ->where('pengajuan_pkl.nim', $mahasiswa->nim)
                ->latest('pengajuan_pkl.created_at')
                ->first();
        }

        $extraProfile = session('extra_profile_' . $user->id, [
            'alamat'   => 'Kampus Baru UI Depok, Kota Depok, Jawa Barat',
            'semester' => '5',
            'ipk'      => '3.85',
            'skills'   => ['UI/UX Design', 'Web Development', 'Figma', 'JavaScript'],
        ]);

        return view('mahasiswa.profil', compact('mahasiswa', 'pengajuan', 'extraProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'no_hp'    => 'required|string|max:20',
            'alamat'   => 'nullable|string|max:255',
            'semester' => 'nullable|integer|min:1|max:14',
            'ipk'      => 'nullable|string|max:10',
            'skills'   => 'nullable|string',
        ]);

        $user = Auth::user();
        if ($user->mahasiswa) {
            DB::table('mahasiswa')
                ->where('nim', $user->mahasiswa->nim)
                ->update([
                    'no_hp'      => $request->no_hp,
                    'updated_at' => now(),
                ]);
        }

        $skillsArray = $request->skills
            ? array_filter(array_map('trim', explode(',', $request->skills)))
            : [];

        session([
            'extra_profile_' . $user->id => [
                'alamat'   => $request->alamat ?? '-',
                'semester' => $request->semester ?? '5',
                'ipk'      => $request->ipk ?? '3.85',
                'skills'   => $skillsArray,
            ]
        ]);

        return back()->with('success', 'Profil mahasiswa berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password'   => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Password akun berhasil diubah!');
    }
}