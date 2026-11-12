@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 1.25rem 0 0.5rem;
    }

    .stepper-wrapper::before {
        content: "";
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 3px;
        background: var(--border-color);
        z-index: 1;
    }

    .stepper-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-counter {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 2px solid var(--border-color);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .stepper-item.completed .step-counter {
        background: var(--navy);
        border-color: var(--navy);
        color: #FFFFFF;
        box-shadow: 0 4px 10px rgba(23, 54, 93, 0.25);
    }

    .stepper-item.active .step-counter {
        background: var(--pale-blue);
        border-color: var(--blue);
        color: var(--navy);
        box-shadow: 0 0 0 4px rgba(139, 174, 214, 0.3);
    }

    .step-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .stepper-item.completed .step-name,
    .stepper-item.active .step-name {
        color: var(--navy);
    }

    .badge-success-soft { background: #E5F4EA; color: #278052; font-weight: 600; }
    .badge-warning-soft { background: #FFF2D7; color: #A86D12; font-weight: 600; }
    .badge-error-soft   { background: #FCE9E8; color: #B94141; font-weight: 600; }
    .badge-info-soft    { background: #E7F0FC; color: #346BA8; font-weight: 600; }

    .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>
@endpush

@section('content')
@php
    $mahasiswa = Auth::user()->mahasiswa;
    $pengajuan = $mahasiswa ? \App\Models\PengajuanPkl::with(['instansi', 'dosbingTerpilih', 'pembimbingIndustri'])
                    ->where('nim', $mahasiswa->nim)->latest()->first() : null;

    $currentStep = 1;
    if ($pengajuan) {
        if ($pengajuan->status_verifikasi == 'pending') $currentStep = 1;
        elseif ($pengajuan->status_verifikasi == 'revisi') $currentStep = 2;
        elseif ($pengajuan->status_verifikasi == 'disetujui_admin') $currentStep = 3;
        elseif ($pengajuan->status_verifikasi == 'aktif_pkl') $currentStep = 4;
    }

    $totalLogbook = $pengajuan ? \App\Models\LogbookHarian::where('pengajuan_id', $pengajuan->id)->count() : 0;
    $targetLogbook = 20; 
    $persenLogbook = min(100, round(($totalLogbook / $targetLogbook) * 100));
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--navy);">
            Halo, {{ explode(' ', $mahasiswa->nama_lengkap ?? 'Mahasiswa')[0] }}! 👋
        </h4>
        <p class="text-muted small mb-0">Selamat datang di Sistem Informasi Magang TIK</p>
    </div>
    <div class="mt-2 mt-md-0">
        <span class="badge px-3 py-2 rounded-pill badge-info-soft border">
            <i class="bi bi-mortarboard me-1"></i>
            Prodi: {{ $mahasiswa->prodi ?? 'Teknik Informatika' }} ({{ $mahasiswa->kelas ?? '-' }})
        </span>
    </div>
</div>

<div class="card-modern p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold mb-0">Progress Magang</h6>
        @if(!$pengajuan)
            <a href="#" class="btn btn-sm text-white fw-semibold px-3" style="background: var(--navy); border-radius: 8px;">
                <i class="bi bi-plus-lg me-1"></i> Ajukan PKL Sekarang
            </a>
        @endif
    </div>

    <div class="stepper-wrapper">
        @php
            $steps = ['Pengajuan', 'Verifikasi', 'Penetapan', 'Pelaksanaan', 'Penilaian'];
        @endphp
        @foreach($steps as $index => $label)
            @php
                $stepNum = $index + 1;
                $class = $stepNum < $currentStep ? 'completed' : ($stepNum == $currentStep ? 'active' : '');
            @endphp
            <div class="stepper-item {{ $class }}">
                <div class="step-counter">
                    @if($stepNum < $currentStep)
                        <i class="bi bi-check-lg"></i>
                    @else
                        {{ $stepNum }}
                    @endif
                </div>
                <div class="step-name">{{ $label }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- 4 Grid Status Cards sesuai Figma -->
<div class="row g-3 mb-4">
    <!-- Status Magang -->
    <div class="col-md-6">
        <div class="card-modern p-3 h-100">
            <div class="small fw-semibold text-muted mb-2">Status Magang</div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box badge-success-soft">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    @if($pengajuan && $pengajuan->status_verifikasi == 'aktif_pkl')
                        <div class="fw-bold fs-5 text-success">Aktif</div>
                        <span class="badge rounded-pill badge-success-soft mt-1">
                            <i class="bi bi-dot"></i> Sedang Berlangsung
                        </span>
                    @elseif($pengajuan)
                        <div class="fw-bold fs-5 text-warning text-capitalize">{{ str_replace('_', ' ', $pengajuan->status_verifikasi) }}</div>
                        <span class="badge rounded-pill badge-warning-soft mt-1">Menunggu Proses</span>
                    @else
                        <div class="fw-bold fs-5 text-secondary">Belum Mengajukan</div>
                        <span class="badge rounded-pill badge-error-soft mt-1">Segera Lengkapi Berkas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pengingat -->
    <div class="col-md-6">
        <div class="card-modern p-3 h-100">
            <div class="small fw-semibold text-muted mb-2">Pengingat</div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box badge-error-soft">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div>
                    <div class="fw-bold">Pengumpulan Laporan Akhir</div>
                    <small class="fw-semibold" style="color: #B94141;">14 - 24 Desember 2026</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Bimbingan -->
    <div class="col-md-6">
        <div class="card-modern p-3 h-100">
            <div class="small fw-semibold text-muted mb-2">Jadwal Bimbingan Terdekat</div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box badge-info-soft">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="fw-bold">Belum Ada Jadwal Terdekat</div>
                    <small class="text-muted">Ajukan jadwal bimbingan pada menu Bimbingan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Logbook -->
    <div class="col-md-6">
        <div class="card-modern p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">Progress Logbook</span>
                <span class="small fw-bold" style="color: var(--navy);">{{ $totalLogbook }} dari {{ $targetLogbook }} Kegiatan</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box badge-warning-soft">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="fw-bold fs-5" style="color: #B94141;">{{ $persenLogbook }}%</span>
                        <a href="#" class="small text-decoration-none fw-semibold" style="color: var(--blue);">+ Isi Logbook</a>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px; background: var(--subtle);">
                        <div class="progress-bar" role="progressbar" style="width: {{ $persenLogbook }}%; background: var(--navy);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Magang Card -->
<div class="card-modern p-4 mb-4">
    <h6 class="fw-bold mb-3">Informasi Magang</h6>
    <div class="row g-3 align-items-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                <div class="icon-box bg-white border shadow-sm" style="color: var(--navy);">
                    <i class="bi bi-building fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">{{ $pengajuan->instansi->nama_perusahaan ?? 'Belum Terdaftar di Perusahaan' }}</h6>
                    <div class="small text-muted mb-1">
                        <span class="fw-semibold text-dark">Bidang:</span> {{ $pengajuan->instansi->bidang_industri ?? '-' }}
                    </div>
                    <div class="small text-muted">
                        <span class="fw-semibold text-dark">Periode:</span>
                        @if($pengajuan)
                            {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row g-2">
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-person-badge text-primary"></i>
                            <small class="text-muted fw-semibold">Dosen Pembimbing PNJ</small>
                        </div>
                        <div class="fw-bold small">{{ $pengajuan->dosbingTerpilih->nama_lengkap ?? 'Belum Ditetapkan' }}</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-person-workspace text-success"></i>
                            <small class="text-muted fw-semibold">Pembimbing Industri</small>
                        </div>
                        <div class="fw-bold small">{{ $pengajuan->pembimbingIndustri->nama_lengkap ?? 'Belum Ditetapkan' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-modern p-4">
    <h6 class="fw-bold mb-3">Pengumuman</h6>
    <div class="p-3 rounded-3" style="background: var(--bg-main); border: 1px solid var(--border-color);">
        <ol class="mb-0 ps-3 small text-muted lh-lg">
            <li>Jadwal bimbingan terbaru telah tersedia. Silakan cek menu Bimbingan secara berkala.</li>
            <li>Pastikan mengisi Logbook Harian setiap hari kerja agar dapat divalidasi oleh Dosen Pembimbing dan Pembimbing Industri.</li>
        </ol>
    </div>
</div>
@endsection