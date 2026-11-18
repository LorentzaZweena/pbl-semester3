<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DokumenPklController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuanAktif = null;
        $dokumen = null;
        $daftarForm = [];

        if ($mahasiswa) {
            $pengajuanAktif = DB::table('pengajuan_pkl')
                ->where('nim', $mahasiswa->nim)
                ->latest('created_at')
                ->first();

            if ($pengajuanAktif) {
                $dokumen = DB::table('dokumen_akhir')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->first();

                $userReq = DB::table('user_requirements')->where('pengajuan_id', $pengajuanAktif->id)->first();

                $logbookHarianCount = DB::table('logbook_harian')->where('pengajuan_id', $pengajuanAktif->id)->count();

                $logbookApproved = DB::table('logbook_harian')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->where('status_validasi', 'disetujui')
                    ->count();

                $bimbinganApproved = DB::table('logbook_bimbingan')
                    ->where('pengajuan_id', $pengajuanAktif->id)
                    ->where('status_validasi', 'disetujui')
                    ->count();

                $penilaian = DB::table('penilaian_pkl')->where('pengajuan_id', $pengajuanAktif->id)->first();

                $daftarForm = [
                    [
                        'kode'         => 'F1',
                        'nama'         => 'Pernyataan Kesediaan Pembimbing Kampus',
                        'keterangan'   => 'Formulir kesediaan dosen pembimbing PKL',
                        'deskripsi'    => 'Unggah scan Form F1 yang telah ditandatangani oleh Dosen Pembimbing Kampus dalam format PDF.',
                        'icon'         => 'bi-file-earmark-check',
                        'status_type'  => ($dokumen && $dokumen->file_form_f1) ? 'approved' : 'rejected',
                        'status_label' => ($dokumen && $dokumen->file_form_f1) ? 'Disetujui / Terunggah' : 'Belum Diunggah',
                        'can_upload'   => true,
                        'field_name'   => 'file_form_f1',
                        'file_url'     => ($dokumen && $dokumen->file_form_f1) ? asset('storage/' . $dokumen->file_form_f1) : '',
                    ],
                    [
                        'kode'         => 'F2',
                        'nama'         => 'Form User Requirement',
                        'keterangan'   => 'Spesifikasi modul pekerjaan yang divalidasi industri',
                        'deskripsi'    => 'Form F2 terisi otomatis berdasarkan modul User Requirement yang telah divalidasi oleh Pembimbing Industri.',
                        'icon'         => 'bi-list-check',
                        'status_type'  => $userReq ? ($userReq->status_validasi_industri === 'valid' ? 'approved' : 'pending') : 'rejected',
                        'status_label' => $userReq ? ($userReq->status_validasi_industri === 'valid' ? 'Disetujui' : 'Menunggu Validasi') : 'Belum Diisi',
                        'can_upload'   => false,
                        'field_name'   => '',
                        'file_url'     => '',
                    ],
                    [
                        'kode'         => 'F3',
                        'nama'         => 'Logbook Pembimbing Industri',
                        'keterangan'   => 'Rekapitulasi kegiatan harian selama PKL',
                        'deskripsi'    => 'Form F3 otomatis direkap dari menu Logbook Harian yang telah divalidasi oleh Pembimbing Industri.',
                        'icon'         => 'bi-journal-bookmark',
                        'status_type'  => $logbookApproved > 0 ? 'approved' : ($logbookHarianCount > 0 ? 'pending' : 'rejected'),
                        'status_label' => $logbookApproved > 0 ? 'Disetujui (' . $logbookApproved . ' Entri)' : ($logbookHarianCount > 0 ? 'Menunggu Persetujuan' : 'Belum Ada Logbook'),
                        'can_upload'   => false,
                        'field_name'   => '',
                        'file_url'     => '',
                    ],
                    [
                        'kode'         => 'F4',
                        'nama'         => 'Bukti Bimbingan Pembimbing Kampus',
                        'keterangan'   => 'Catatan asistensi dan bimbingan laporan PKL',
                        'deskripsi'    => 'Form F4 terhubung otomatis dengan riwayat bimbingan pada menu Bimbingan.',
                        'icon'         => 'bi-people',
                        'status_type'  => $bimbinganApproved > 0 ? 'approved' : 'rejected',
                        'status_label' => $bimbinganApproved > 0 ? 'Disetujui (' . $bimbinganApproved . ' Sesi)' : 'Belum Tervalidasi',
                        'can_upload'   => false,
                        'field_name'   => '',
                        'file_url'     => '',
                    ],
                    [
                        'kode'         => 'F5',
                        'nama'         => 'Nilai Pembimbing Industri',
                        'keterangan'   => 'Penilaian softskill & hardskill dari perusahaan',
                        'deskripsi'    => 'Form F5 bersifat rahasia dan diisi langsung oleh Pembimbing Industri melalui akun mitra.',
                        'icon'         => 'bi-lock',
                        'status_type'  => ($penilaian && $penilaian->rata_rata_industri) ? 'approved' : 'locked',
                        'status_label' => ($penilaian && $penilaian->rata_rata_industri) ? 'Sudah Dinilai' : 'Diisi oleh pembimbing industri',
                        'can_upload'   => false,
                        'field_name'   => '',
                        'file_url'     => '',
                    ],
                    [
                        'kode'         => 'F6',
                        'nama'         => 'Nilai Pembimbing Kampus',
                        'keterangan'   => 'Penilaian bimbingan, laporan, dan presentasi',
                        'deskripsi'    => 'Form F6 diisi langsung oleh Dosen Pembimbing Kampus setelah sidang/presentasi PKL.',
                        'icon'         => 'bi-lock',
                        'status_type'  => ($penilaian && $penilaian->rata_rata_kampus) ? 'approved' : 'locked',
                        'status_label' => ($penilaian && $penilaian->rata_rata_kampus) ? 'Sudah Dinilai' : 'Diisi oleh pembimbing kampus',
                        'can_upload'   => false,
                        'field_name'   => '',
                        'file_url'     => '',
                    ],
                    [
                        'kode'         => 'F7',
                        'nama'         => 'Surat Persetujuan Pengumpulan Laporan',
                        'keterangan'   => 'Lembar persetujuan pengumpulan laporan akhir PKL',
                        'deskripsi'    => 'Unggah scan Form F7 yang telah ditandatangani sebelum mengunggah Laporan Akhir ke Repository.',
                        'icon'         => 'bi-file-earmark-medical',
                        'status_type'  => ($dokumen && $dokumen->file_form_f7) ? 'approved' : 'rejected',
                        'status_label' => ($dokumen && $dokumen->file_form_f7) ? 'Disetujui / Terunggah' : 'Belum Diunggah',
                        'can_upload'   => true,
                        'field_name'   => 'file_form_f7',
                        'file_url'     => ($dokumen && $dokumen->file_form_f7) ? asset('storage/' . $dokumen->file_form_f7) : '',
                    ],
                ];
            }
        }

        $metadata = session('repo_metadata_' . ($pengajuanAktif->id ?? 0), [
            'judul'       => '',
            'kategori'    => 'PKL',
            'subkategori' => $mahasiswa->prodi ?? 'TI',
            'bahasa'      => 'Indonesia',
            'kata_kunci'  => '',
        ]);

        return view('mahasiswa.dokumen', compact('mahasiswa', 'pengajuanAktif', 'dokumen', 'daftarForm', 'metadata'));
    }

    public function uploadForm(Request $request)
    {
        $request->validate([
            'field_name'   => 'required|in:file_form_f1,file_form_f7',
            'dokumen_file' => 'required|mimes:pdf|max:2048',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuan = DB::table('pengajuan_pkl')->where('nim', $mahasiswa->nim)->latest('created_at')->first();

        $path = $request->file('dokumen_file')->store('dokumen_pkl', 'public');

        DB::table('dokumen_akhir')->updateOrInsert(
            ['pengajuan_id' => $pengajuan->id],
            [
                $request->field_name => $path,
                'updated_at'         => now(),
                'created_at'         => now(),
            ]
        );

        return redirect()->route('mahasiswa.dokumen')->with('success', 'Dokumen berhasil diunggah!');
    }

    public function storeRepository(Request $request)
    {
        $request->validate([
            'file_laporan_akhir_pdf'   => 'nullable|mimes:pdf|max:5120',
            'file_sertifikat_industri' => 'nullable|mimes:pdf,jpg,jpeg,png|max:3072',
            'link_video_demo'          => 'nullable|url',
            'judul_laporan'            => 'required|string|max:255',
            'subkategori'              => 'required|string',
            'bahasa'                   => 'required|string',
            'kata_kunci'               => 'required|string',
        ]);

        $mahasiswa = Auth::user()->mahasiswa;
        $pengajuan = DB::table('pengajuan_pkl')->where('nim', $mahasiswa->nim)->latest('created_at')->first();
        $existing = DB::table('dokumen_akhir')->where('pengajuan_id', $pengajuan->id)->first();

        $dataUpdate = [
            'link_video_demo'          => $request->link_video_demo,
            'status_sinkronisasi_repo' => $request->action_type === 'submit' ? 1 : 0,
            'uploaded_at'              => now(),
            'updated_at'               => now(),
        ];

        if (!$existing) {
            $dataUpdate['created_at'] = now();
        }

        if ($request->hasFile('file_laporan_akhir_pdf')) {
            $dataUpdate['file_laporan_akhir_pdf'] = $request->file('file_laporan_akhir_pdf')->store('laporan_akhir', 'public');
        }

        if ($request->hasFile('file_sertifikat_industri')) {
            $dataUpdate['file_sertifikat_industri'] = $request->file('file_sertifikat_industri')->store('sertifikat_industri', 'public');
        }

        DB::table('dokumen_akhir')->updateOrInsert(
            ['pengajuan_id' => $pengajuan->id],
            $dataUpdate
        );

        session([
            'repo_metadata_' . $pengajuan->id => [
                'judul'       => $request->judul_laporan,
                'kategori'    => $request->kategori ?? 'PKL',
                'subkategori' => $request->subkategori,
                'bahasa'      => $request->bahasa,
                'kata_kunci'  => $request->kata_kunci,
            ]
        ]);

        $pesan = $request->action_type === 'submit'
            ? 'Laporan Akhir berhasil dikirim dan tersimpan di Repository!'
            : 'Draft laporan dan metadata berhasil disimpan.';

        return redirect()->route('mahasiswa.dokumen', ['tab' => 'repository'])->with('success', $pesan);
    }
}