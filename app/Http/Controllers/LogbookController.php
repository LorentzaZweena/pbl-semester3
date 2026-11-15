<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $selectedWeek = (int) $request->get('week', 1);

        $pengajuanAktif = null;
        $logbooksHarian = collect();
        $selectedWeekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();
        $selectedWeekEnd = Carbon::now()->endOfWeek(Carbon::SATURDAY)->toDateString();

        if ($mahasiswa) {
            $pengajuanAktif = DB::table('pengajuan_pkl')
                ->where('nim', $mahasiswa->nim)
                ->latest('created_at')
                ->first();

            if ($pengajuanAktif) {
                $baseStart = Carbon::parse($pengajuanAktif->tanggal_mulai)->startOfWeek(Carbon::SUNDAY);
                $selectedWeekStart = $baseStart->copy()->addWeeks($selectedWeek - 1)->toDateString();
                $selectedWeekEnd = Carbon::parse($selectedWeekStart)->addDays(6)->toDateString();

                $logbooksHarian = DB::table('logbook_harian')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->where('minggu_ke', $selectedWeek)
                    ->orderBy('tanggal', 'asc')
                    ->get();
            }
        }

        $weekDays = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 0; $i < 7; $i++) {
            $dt = Carbon::parse($selectedWeekStart)->addDays($i);
            $weekDays[] = [
                'date'      => $dt->toDateString(),
                'day_short' => $dayNames[$i],
                'day_num'   => $dt->format('j'),
                'is_sunday' => $i === 0,
            ];
        }

        $totalMingguIni = $logbooksHarian->count();
        $persenKegiatan = min(100, round(($totalMingguIni / 5) * 100));

        $jumlahTervalidasi = $logbooksHarian->filter(function ($item) {
            return $item->status_validasi === 'disetujui' || ($item->status_persetujuan_dosen ?? '') === 'disetujui';
        })->count();

        $persenValidasi = $totalMingguIni > 0 ? round(($jumlahTervalidasi / $totalMingguIni) * 100) : 0;

        return view('mahasiswa.logbook', compact(
            'pengajuanAktif',
            'logbooksHarian',
            'selectedWeek',
            'selectedWeekStart',
            'selectedWeekEnd',
            'weekDays',
            'totalMingguIni',
            'persenKegiatan',
            'jumlahTervalidasi',
            'persenValidasi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'minggu_ke'        => 'required|integer|min:1',
            'aktivitas'        => 'required|string',
            'output'           => 'required|string',
            'dokumentasi_foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuan = DB::table('pengajuan_pkl')
            ->where('nim', $mahasiswa->nim)
            ->latest('created_at')
            ->first();

        if (!$pengajuan) {
            return back()->with('error', 'Pengajuan PKL tidak ditemukan.');
        }

        $fotoPath = null;
        if ($request->hasFile('dokumentasi_foto')) {
            $fotoPath = $request->file('dokumentasi_foto')->store('logbook_dokumentasi', 'public');
        }

        $uraianGabungan = $request->aktivitas . "\n||OUTPUT||\n" . $request->output;

        DB::table('logbook_harian')->insert([
            'pengajuan_id'             => $pengajuan->id,
            'nip'                      => $pengajuan->dosbing_terpilih,
            'minggu_ke'                => $request->minggu_ke,
            'uraian_kegiatan'          => $uraianGabungan,
            'dokumentasi_foto'         => $fotoPath,
            'status_validasi'          => 'menunggu',
            'status_persetujuan_dosen' => 'menunggu',
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        return redirect()->route('mahasiswa.logbook', ['week' => $request->minggu_ke])
            ->with('success', 'Aktivitas harian berhasil ditambahkan ke Logbook!');
    }
}