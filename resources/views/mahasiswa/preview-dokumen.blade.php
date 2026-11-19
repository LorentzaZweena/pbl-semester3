@extends('layouts.mahasiswa')

@section('title', 'Pratinjau Dokumen Rekap Nilai')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/preview-dokumen.css') }}">
@endpush

@section('content')
@php
    $nilaiIndustri = $penilaian->rata_rata_industri ?? null;
    $nilaiKampus   = $penilaian->rata_rata_kampus ?? null;

    $nilaiAkhir = $penilaian->nilai_akhir_angka ?? (
        ($nilaiIndustri !== null && $nilaiKampus !== null)
            ? round(($nilaiIndustri * 0.6) + ($nilaiKampus * 0.4), 2)
            : null
    );

    $nilaiHuruf = $penilaian->nilai_akhir_huruf ?? (
        $nilaiAkhir === null ? '-' : (
            $nilaiAkhir >= 81 ? 'A' : (
                $nilaiAkhir >= 76 ? 'A-' : (
                    $nilaiAkhir >= 72 ? 'B+' : (
                        $nilaiAkhir >= 68 ? 'B' : (
                            $nilaiAkhir >= 64 ? 'B-' : (
                                $nilaiAkhir >= 60 ? 'C+' : (
                                    $nilaiAkhir >= 56 ? 'C' : (
                                        $nilaiAkhir >= 41 ? 'D' : 'E'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    $statusKelulusan = $penilaian->status_kelulusan ?? ($nilaiAkhir !== null && $nilaiAkhir >= 60 ? 'lulus' : 'belum_lengkap');
@endphp

<div class="mb-3">
    <a href="{{ route('mahasiswa.laporan') }}" class="text-decoration-none fw-semibold small" style="color: var(--navy);">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Nilai Akhir
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="preview-pane">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--text-dark);">Pratinjau Dokumen</h5>
                <span class="badge bg-light text-dark border" id="badgePaperInfo">A4 • Portrait</span>
            </div>

            <div class="paper-viewport">
                <div class="a4-sheet" id="dokumenRekapArea">
                    <div class="kop-surat">
                        <div style="font-size: 11pt; font-weight: bold;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</div>
                        <div style="font-size: 13pt; font-weight: bold; color: #17365D;">POLITEKNIK NEGERI JAKARTA</div>
                        <div style="font-size: 11.5pt; font-weight: bold;">JURUSAN TEKNIK INFORMATIKA DAN KOMPUTER</div>
                        <div style="font-size: 9pt; color: #444;">Jl. Prof. DR. G.A. Siwabessy, Kampus Universitas Indonesia, Depok 16425</div>
                    </div>

                    <div class="text-center mb-4">
                        <div style="font-size: 12.5pt; font-weight: bold; text-decoration: underline;">
                            REKAPITULASI NILAI AKHIR PRAKTIK KERJA LAPANGAN (PKL)
                        </div>
                        <div style="font-size: 10pt;">Tahun Akademik 2025/2026</div>
                    </div>

                    <table style="width: 100%; font-size: 10.5pt; margin-bottom: 1rem;">
                        <tr>
                            <td width="190">Nama Mahasiswa</td>
                            <td width="15">:</td>
                            <td><strong>{{ $mahasiswa->nama_lengkap ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td>:</td>
                            <td>{{ $mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Program Studi / Kelas</td>
                            <td>:</td>
                            <td>{{ $mahasiswa->prodi ?? 'TI' }} / {{ $mahasiswa->kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Pelaksanaan PKL</td>
                            <td>:</td>
                            <td>{{ $pengajuanAktif->nama_perusahaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Periode Pelaksanaan</td>
                            <td>:</td>
                            <td>
                                @if($pengajuanAktif)
                                    {{ \Carbon\Carbon::parse($pengajuanAktif->tanggal_mulai)->translatedFormat('d F Y') }} s/d
                                    {{ \Carbon\Carbon::parse($pengajuanAktif->tanggal_selesai)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>

                    <table class="rekap-table">
                        <thead>
                            <tr>
                                <th width="45">No</th>
                                <th>Komponen Penilaian</th>
                                <th width="85">Bobot</th>
                                <th width="95">Nilai</th>
                                <th width="110">Nilai Terbobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <strong>Penilaian Pembimbing Industri (Form F5)</strong><br>
                                    <span style="font-size: 9.5pt; color: #444;">
                                        Softskill: {{ $penilaian->skor_softskill ?? '-' }} | Hardskill: {{ $penilaian->skor_hardskill ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">60%</td>
                                <td class="text-center">{{ $nilaiIndustri !== null ? number_format($nilaiIndustri, 2) : '-' }}</td>
                                <td class="text-center">{{ $nilaiIndustri !== null ? number_format($nilaiIndustri * 0.6, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>
                                    <strong>Penilaian Pembimbing Kampus (Form F6)</strong><br>
                                    <span style="font-size: 9.5pt; color: #444;">
                                        Bimbingan: {{ $penilaian->skor_bimbingan ?? '-' }} | Laporan: {{ $penilaian->skor_laporan ?? '-' }} | Presentasi: {{ $penilaian->skor_presentasi ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">40%</td>
                                <td class="text-center">{{ $nilaiKampus !== null ? number_format($nilaiKampus, 2) : '-' }}</td>
                                <td class="text-center">{{ $nilaiKampus !== null ? number_format($nilaiKampus * 0.4, 2) : '-' }}</td>
                            </tr>
                            <tr style="background: #F8FAFC; font-weight: bold;">
                                <td colspan="2" class="text-end">TOTAL NILAI AKHIR</td>
                                <td class="text-center">100%</td>
                                <td class="text-center">{{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-' }}</td>
                                <td class="text-center">Grade: {{ $nilaiHuruf }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="border: 1px solid #333; padding: 10px 14px; margin-bottom: 2rem; font-size: 10.5pt;">
                        <strong>Status Kelulusan PKL:</strong>
                        @if($statusKelulusan === 'lulus')
                            <span style="color: #1E6641; font-weight: bold;">LULUS (Memenuhi syarat kelulusan minimum nilai C+)</span>
                        @elseif($statusKelulusan === 'tidak_lulus')
                            <span style="color: #B94141; font-weight: bold;">TIDAK LULUS</span>
                        @else
                            <span style="color: #A86D12; font-weight: bold;">BELUM LENGKAP / PROSES PENILAIAN</span>
                        @endif
                    </div>

                    <table style="width: 100%; font-size: 10pt; margin-top: 2.5rem; text-align: center;">
                        <tr>
                            <td width="50%">
                                Mengetahui,<br>
                                Pembimbing Industri<br><br><br><br>
                                <u><strong>{{ $penilaian->nama_industri ?? '( ........................................ )' }}</strong></u>
                            </td>
                            <td width="50%">
                                Depok, {{ now()->translatedFormat('d F Y') }}<br>
                                Dosen Pembimbing Kampus<br><br><br><br>
                                <u><strong>{{ $penilaian->nama_dosen ?? '( ........................................ )' }}</strong></u><br>
                                NIP. {{ $penilaian->nip ?? '........................' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="settings-card">
            <h5 class="fw-bold mb-3" style="color: var(--text-dark);">Pengaturan Cetak</h5>

            <div class="mb-3">
                <label class="form-label fw-bold small">Ukuran Kertas</label>
                <select id="paperSizeSelect" class="form-select rounded-3" onchange="updatePaperPreview()">
                    <option value="a4" selected>A4 (210 x 297 mm)</option>
                    <option value="letter">Letter (216 x 279 mm)</option>
                    <option value="legal">F4 / Legal (216 x 356 mm)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Orientasi</label>
                <select id="orientationSelect" class="form-select rounded-3" onchange="updatePaperPreview()">
                    <option value="portrait" selected>Portrait</option>
                    <option value="landscape">Landscape</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Halaman</label>
                <select class="form-select rounded-3">
                    <option value="all" selected>Semua Halaman (1)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">Jumlah Salinan</label>
                <input type="number" class="form-control rounded-3" value="1" min="1" max="10">
            </div>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-download-pdf" id="btnDownloadPdf" onclick="downloadRekapPdf()">
                    <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> Download PDF
                </button>

                <button type="button" class="btn btn-print-doc" onclick="window.print()">
                    <i class="bi bi-printer-fill me-2"></i> Print / Cetak
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function updatePaperPreview() {
        const size = document.getElementById('paperSizeSelect').value;
        const orientation = document.getElementById('orientationSelect').value;
        const sheet = document.getElementById('dokumenRekapArea');
        const badge = document.getElementById('badgePaperInfo');

        sheet.classList.toggle('landscape-mode', orientation === 'landscape');
        badge.textContent = `${size.toUpperCase()} • ${orientation.charAt(0).toUpperCase() + orientation.slice(1)}`;
    }

    function downloadRekapPdf() {
        const btn = document.getElementById('btnDownloadPdf');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyiapkan PDF...';
        btn.disabled = true;

        const element = document.getElementById('dokumenRekapArea');
        const paperSize = document.getElementById('paperSizeSelect').value;
        const orientation = document.getElementById('orientationSelect').value;
        const nim = "{{ $mahasiswa->nim ?? 'Mahasiswa' }}";

        const opt = {
            margin:       10,
            filename:     `Rekap_Nilai_PKL_${nim}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: paperSize, orientation: orientation }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endpush