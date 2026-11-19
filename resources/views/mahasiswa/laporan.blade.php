@extends('layouts.mahasiswa')

@section('title', 'Laporan & Nilai Akhir PKL')

@push('styles')
<style>
    .outer-panel {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        box-shadow: 0 2px 10px rgba(23, 54, 93, 0.03);
    }

    .score-box {
        background: #FFFFFF;
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        padding: 1.35rem 1rem;
        text-align: center;
        transition: all 0.2s ease;
        height: 100%;
    }

    .score-box:hover {
        border-color: var(--soft-blue);
        box-shadow: 0 6px 16px rgba(23, 54, 93, 0.06);
        transform: translateY(-2px);
    }

    .score-box.final-box {
        background: #F4F8FD;
        border-color: var(--blue);
    }

    .score-title {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--text-dark);
        margin-bottom: 0.65rem;
    }

    .score-number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--navy);
        line-height: 1.1;
        margin-bottom: 0.35rem;
    }

    .score-sub {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .math-symbol {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--navy);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .aspect-table-wrapper {
        background: #FAFBFD;
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
        height: 100%;
    }

    .aspect-table {
        margin-bottom: 0;
    }

    .aspect-table th {
        background: #F3F6FA;
        color: var(--text-dark);
        font-weight: 700;
        font-size: 0.92rem;
        padding: 0.95rem 1.25rem;
        border-bottom: 1.5px solid var(--border-color);
    }

    .aspect-table td {
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .aspect-table tr:last-child td {
        border-bottom: none;
        background: #F3F6FA;
        font-weight: 700;
    }

    .graduation-card {
        background: #FFFFFF;
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .graduation-inner {
        flex-grow: 1;
        border-radius: 12px;
        padding: 1.5rem 1rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .grad-lulus {
        background: #C7EED3;
        color: #163A28;
    }

    .grad-belum {
        background: #FFF2D7;
        color: #7A4E0B;
    }

    .grad-gagal {
        background: #FCE9E8;
        color: #8E2828;
    }

    .grad-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--navy);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 6px 14px rgba(23, 54, 93, 0.2);
    }

    .btn-navy {
        background: var(--navy);
        color: #FFFFFF;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.6rem 1.4rem;
        border: none;
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }

    .btn-navy:hover {
        background: var(--blue);
        color: #FFFFFF;
        transform: translateY(-1px);
    }

    @media print {
        .sidebar, .topbar, .no-print {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
        }
        .content-area {
            padding: 0 !important;
        }
        .outer-panel {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .print-header {
            display: block !important;
        }
    }
</style>
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
                    $nilaiAkhir >= 71 ? 'B+' : (
                        $nilaiAkhir >= 66 ? 'B' : (
                            $nilaiAkhir >= 61 ? 'B-' : (
                                $nilaiAkhir >= 56 ? 'C+' : (
                                    $nilaiAkhir >= 51 ? 'C' : 'E'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    $statusKelulusan = $penilaian->status_kelulusan ?? 'belum_lengkap';
@endphp

<div class="outer-panel">
    <div class="print-header d-none mb-4 pb-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0">POLITEKNIK NEGERI JAKARTA</h5>
                <div class="small text-muted">Jurusan Teknik Informatika dan Komputer — Rekapitulasi Nilai Akhir PKL</div>
            </div>
            <div class="text-end small">
                <div><strong>Nama:</strong> {{ $mahasiswa->nama_lengkap ?? '-' }}</div>
                <div><strong>NIM:</strong> {{ $mahasiswa->nim ?? '-' }} ({{ $mahasiswa->prodi ?? 'TI' }})</div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-dark);">Nilai Akhir</h4>
            <p class="text-muted small mb-0">Hasil penilaian PKL dari pembimbing industri (60%) dan pembimbing kampus (40%).</p>
        </div>

        @if($pengajuanAktif && $pengajuanAktif->nama_perusahaan)
            <span class="badge px-3 py-2 rounded-pill no-print" style="background: #E7F0FC; color: #346BA8;">
                <i class="bi bi-building me-1"></i> {{ $pengajuanAktif->nama_perusahaan }}
            </span>
        @endif
    </div>

    @if(!$pengajuanAktif)
        <div class="text-center py-5 border rounded-4 bg-light">
            <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold">Belum Ada Data Penilaian PKL</h6>
            <p class="text-muted small mb-3">Anda belum memiliki pengajuan PKL aktif. Nilai akhir akan tampil setelah pembimbing industri dan kampus menginput nilai.</p>
            <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-navy">Buat Pengajuan PKL</a>
        </div>
    @else

    <div class="row align-items-center g-2 g-md-3 mb-4">
        <div class="col-md">
            <div class="score-box">
                <div class="score-title">Nilai Industri</div>
                <div class="score-number">{{ $nilaiIndustri !== null ? number_format($nilaiIndustri, 0) : '-' }}</div>
                <div class="score-sub">(60%)</div>
            </div>
        </div>

        <div class="col-md-auto math-symbol py-1">+</div>
        <div class="col-md">
            <div class="score-box">
                <div class="score-title">Nilai Kampus</div>
                <div class="score-number">{{ $nilaiKampus !== null ? number_format($nilaiKampus, 0) : '-' }}</div>
                <div class="score-sub">(40%)</div>
            </div>
        </div>

        <div class="col-md-auto math-symbol py-1">=</div>

        <div class="col-md">
            <div class="score-box final-box">
                <div class="score-title">Nilai Akhir</div>
                <div class="score-number">{{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 0) : '-' }}</div>
                <div class="score-sub" style="color: var(--navy);">
                    @if($nilaiAkhir !== null)
                        (Grade {{ $nilaiHuruf }} • {{ $statusKelulusan === 'lulus' ? 'Lulus' : ($statusKelulusan === 'tidak_lulus' ? 'Tidak Lulus' : 'Proses') }})
                    @else
                        (Menunggu Nilai)
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="aspect-table-wrapper">
                <table class="table aspect-table">
                    <thead>
                        <tr>
                            <th>Aspek Penilaian</th>
                            <th class="text-center" width="120">Bobot</th>
                            <th class="text-center" width="120">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">Penilaian Pembimbing Industri (F5)</div>
                                <small class="text-muted">
                                    Softskill: {{ $penilaian->skor_softskill ?? '-' }} •
                                    Hardskill: {{ $penilaian->skor_hardskill ?? '-' }}
                                </small>
                            </td>
                            <td class="text-center fw-bold">60%</td>
                            <td class="text-center fw-bold fs-6">{{ $nilaiIndustri !== null ? number_format($nilaiIndustri, 0) : '-' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">Penilaian Pembimbing Kampus (F6)</div>
                                <small class="text-muted">
                                    Bimbingan: {{ $penilaian->skor_bimbingan ?? '-' }} •
                                    Laporan: {{ $penilaian->skor_laporan ?? '-' }} •
                                    Presentasi: {{ $penilaian->skor_presentasi ?? '-' }}
                                </small>
                            </td>
                            <td class="text-center fw-bold">40%</td>
                            <td class="text-center fw-bold fs-6">{{ $nilaiKampus !== null ? number_format($nilaiKampus, 0) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fs-6" style="color: var(--navy);">Nilai Akhir</td>
                            <td class="text-center fs-6" style="color: var(--navy);">100%</td>
                            <td class="text-center fs-5" style="color: var(--navy);">
                                {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 0) : '-' }}
                                @if($nilaiHuruf !== '-')
                                    <span class="badge ms-1" style="background: var(--navy); font-size: 0.75rem;">{{ $nilaiHuruf }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="graduation-card">
                <h6 class="fw-bold mb-3" style="color: var(--text-dark);">Status Kelulusan</h6>

                @if($statusKelulusan === 'lulus')
                    <div class="graduation-inner grad-lulus">
                        <div class="grad-icon-circle">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Lulus</h4>
                        <p class="small mb-0 fw-medium">Anda dinyatakan lulus PKL.</p>
                    </div>
                @elseif($statusKelulusan === 'tidak_lulus')
                    <div class="graduation-inner grad-gagal">
                        <div class="grad-icon-circle" style="background: #B94141;">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Tidak Lulus</h4>
                        <p class="small mb-0 fw-medium">Silakan hubungi Koordinator PKL / Prodi.</p>
                    </div>
                @else
                    <div class="graduation-inner grad-belum">
                        <div class="grad-icon-circle" style="background: #A86D12;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Belum Lengkap</h5>
                        <p class="small mb-0 fw-medium">Menunggu kelengkapan nilai dari pembimbing industri & kampus.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 no-print">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Evaluator: <strong>{{ $penilaian->nama_industri ?? 'Pembimbing Industri' }}</strong> & <strong>{{ $penilaian->nama_dosen ?? 'Dosen Pembimbing' }}</strong>
        </small>

        <a href="{{ route('mahasiswa.laporan.pratinjau') }}" class="btn btn-navy">
            <i class="bi bi-file-earmark-text me-1"></i> Unduh Rekap
        </a>
    </div>
    @endif
</div>
@endsection