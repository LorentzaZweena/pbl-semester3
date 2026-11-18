<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $lastReadAt = session('notif_read_at_' . Auth::id());
        $notifications = [];

        if ($mahasiswa) {
            $pengajuanList = DB::table('pengajuan_pkl')
                ->leftJoin('instansi_mitra', 'pengajuan_pkl.instansi_id', '=', 'instansi_mitra.id')
                ->select('pengajuan_pkl.*', 'instansi_mitra.nama_perusahaan')
                ->where('pengajuan_pkl.nim', $mahasiswa->nim)
                ->orderByDesc('pengajuan_pkl.updated_at')
                ->get();

            foreach ($pengajuanList as $p) {
                $statusText = match ($p->status_verifikasi) {
                    'disetujui_admin' => 'Pengajuan PKL Anda di ' . ($p->nama_perusahaan ?? 'Instansi') . ' telah disetujui oleh Admin Prodi.',
                    'aktif_pkl'       => 'Status PKL Anda di ' . ($p->nama_perusahaan ?? 'Instansi') . ' kini telah AKTIF. Selamat melaksanakan PKL!',
                    'revisi'          => 'Pengajuan PKL memerlukan revisi. Catatan Admin: ' . ($p->catatan_admin ?? 'Silakan periksa berkas Anda.'),
                    'ditolak'         => 'Pengajuan PKL ditolak. Catatan: ' . ($p->catatan_admin ?? '-'),
                    default           => 'Pengajuan PKL Anda di ' . ($p->nama_perusahaan ?? 'Instansi') . ' berhasil dikirim dan sedang menunggu verifikasi Admin Prodi.',
                };

                $titleClass = match ($p->status_verifikasi) {
                    'revisi', 'ditolak' => 'danger',
                    'pending'           => 'warning',
                    default             => '',
                };

                $dt = Carbon::parse($p->updated_at ?? $p->created_at);
                $notifications[] = [
                    'category'       => 'pengajuan',
                    'title'          => 'Pembaruan Status Pengajuan PKL',
                    'title_class'    => $titleClass,
                    'message'        => $statusText,
                    'timestamp'      => $dt->timestamp,
                    'date_formatted' => $dt->translatedFormat('d F Y'),
                    'is_unread'      => !$lastReadAt || $dt->gt(Carbon::parse($lastReadAt)),
                    'icon'           => 'bi-file-earmark-check-fill',
                    'bg_icon'        => '#E5F4EA',
                    'color_icon'     => '#278052',
                    'action_url'     => route('mahasiswa.pengajuan'),
                    'action_label'   => 'Lihat Pengajuan PKL',
                ];
            }

            $pengajuanAktif = $pengajuanList->first();

            if ($pengajuanAktif) {
                $logbooks = DB::table('logbook_harian')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->orderByDesc('updated_at')
                    ->limit(5)
                    ->get();

                foreach ($logbooks as $log) {
                    $tglLog = Carbon::parse($log->tanggal)->translatedFormat('d F Y');
                    if ($log->status_persetujuan_dosen === 'disetujui') {
                        $msg = "Logbook tanggal {$tglLog} (Minggu ke-{$log->minggu_ke}) telah disetujui dan ditandatangani oleh Dosen Pembimbing.";
                        $tClass = '';
                    } elseif ($log->status_validasi === 'disetujui') {
                        $msg = "Logbook tanggal {$tglLog} (Minggu ke-{$log->minggu_ke}) telah divalidasi oleh Pembimbing Industri.";
                        $tClass = '';
                    } elseif ($log->status_validasi === 'ditolak' || $log->status_persetujuan_dosen === 'revisi') {
                        $msg = "Logbook tanggal {$tglLog} memerlukan perbaikan. Catatan: " . ($log->catatan_pembimbing ?? 'Periksa kembali uraian kegiatan.');
                        $tClass = 'danger';
                    } else {
                        $msg = "Catatan logbook tanggal {$tglLog} berhasil disimpan dan menunggu validasi pembimbing.";
                        $tClass = 'info';
                    }

                    $dt = Carbon::parse($log->updated_at ?? $log->created_at);
                    $notifications[] = [
                        'category'       => 'logbook',
                        'title'          => 'Informasi Validasi Logbook',
                        'title_class'    => $tClass,
                        'message'        => $msg,
                        'timestamp'      => $dt->timestamp,
                        'date_formatted' => $dt->translatedFormat('d F Y'),
                        'is_unread'      => !$lastReadAt || $dt->gt(Carbon::parse($lastReadAt)),
                        'icon'           => 'bi-journal-check',
                        'bg_icon'        => '#E7F0FC',
                        'color_icon'     => '#346BA8',
                        'action_url'     => route('mahasiswa.logbook', ['week' => $log->minggu_ke]),
                        'action_label'   => 'Buka Logbook',
                    ];
                }

                $bimbingans = DB::table('logbook_bimbingan')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->orderByDesc('updated_at')
                    ->limit(5)
                    ->get();

                foreach ($bimbingans as $bim) {
                    $tglBim = Carbon::parse($bim->tanggal_bimbingan)->translatedFormat('d F Y');
                    $tipe = ucfirst($bim->tipe_pembimbing);
                    $msg = $bim->status_validasi === 'disetujui'
                        ? "Sesi bimbingan {$tipe} pada {$tglBim} (Topik: {$bim->topik_bahasan}) telah disetujui."
                        : "Jadwal/catatan bimbingan {$tipe} pada {$tglBim} (Topik: {$bim->topik_bahasan}) telah tercatat.";

                    $dt = Carbon::parse($bim->updated_at ?? $bim->created_at);
                    $notifications[] = [
                        'category'       => 'bimbingan_nilai',
                        'title'          => 'Jadwal & Catatan Bimbingan Baru',
                        'title_class'    => '',
                        'message'        => $msg,
                        'timestamp'      => $dt->timestamp,
                        'date_formatted' => $dt->translatedFormat('d F Y'),
                        'is_unread'      => !$lastReadAt || $dt->gt(Carbon::parse($lastReadAt)),
                        'icon'           => 'bi-calendar-event-fill',
                        'bg_icon'        => '#FFF2D7',
                        'color_icon'     => '#A86D12',
                        'action_url'     => route('mahasiswa.dashboard'),
                        'action_label'   => 'Lihat Jadwal Bimbingan',
                    ];
                }

                $nilai = DB::table('penilaian_pkl')->where('pengajuan_id', $pengajuanAktif->id)->first();
                if ($nilai && ($nilai->rata_rata_industri || $nilai->rata_rata_kampus)) {
                    $dt = Carbon::parse($nilai->updated_at ?? $nilai->created_at);
                    $notifications[] = [
                        'category'       => 'bimbingan_nilai',
                        'title'          => 'Pembaruan Form Penilaian PKL (F5 / F6)',
                        'title_class'    => '',
                        'message'        => 'Pembimbing telah memperbarui komponen penilaian PKL Anda. Silakan cek status kelengkapan dokumen F5 & F6 di menu Dokumen.',
                        'timestamp'      => $dt->timestamp,
                        'date_formatted' => $dt->translatedFormat('d F Y'),
                        'is_unread'      => !$lastReadAt || $dt->gt(Carbon::parse($lastReadAt)),
                        'icon'           => 'bi-award-fill',
                        'bg_icon'        => '#E5F4EA',
                        'color_icon'     => '#278052',
                        'action_url'     => route('mahasiswa.dokumen'),
                        'action_label'   => 'Cek Dokumen F5 & F6',
                    ];
                }
            }
        }

        usort($notifications, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return view('mahasiswa.notifikasi', compact('notifications'));
    }

    public function markAllRead()
    {
        session(['notif_read_at_' . Auth::id() => now()->toDateTimeString()]);
        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}