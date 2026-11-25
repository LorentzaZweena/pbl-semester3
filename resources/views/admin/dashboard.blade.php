@extends('layouts.admin')

@section('title', 'Dashboard Admin Prodi')

@push('styles')
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color: var(--text-dark);">Dashboard Admin Program Studi</h4>
    <p class="text-muted small mb-0">Ringkasan verifikasi pengajuan PKL, kuota dosen pembimbing, dan monitoring mahasiswa.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">Menunggu Verifikasi</span>
                <span class="badge rounded-circle p-2" style="background: #FFF2D7; color: #A86D12;"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <h3 class="fw-bold mb-0" style="color: #A86D12;">{{ $stats['pending'] }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Pengajuan PKL baru</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">Perlu Revisi</span>
                <span class="badge rounded-circle p-2" style="background: #FCE9E8; color: #B94141;"><i class="bi bi-exclamation-circle"></i></span>
            </div>
            <h3 class="fw-bold mb-0" style="color: #B94141;">{{ $stats['revisi'] }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Menunggu perbaikan mhs</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">Mahasiswa Aktif PKL</span>
                <span class="badge rounded-circle p-2" style="background: #E5F4EA; color: #278052;"><i class="bi bi-briefcase"></i></span>
            </div>
            <h3 class="fw-bold mb-0" style="color: #278052;">{{ $stats['aktif'] }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Sedang melaksanakan PKL</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-muted">Sisa Kuota Dosbing</span>
                <span class="badge rounded-circle p-2" style="background: #E7F0FC; color: #346BA8;"><i class="bi bi-person-check"></i></span>
            </div>
            <h3 class="fw-bold mb-0" style="color: var(--navy);">{{ $stats['sisa_kuota'] }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Slot bimbingan tersedia</small>
        </div>
    </div>
</div>

<div class="outer-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Antrean Verifikasi Pengajuan PKL</h5>
            <small class="text-muted">Pengajuan mahasiswa terbaru yang membutuhkan tindakan Admin Prodi</small>
        </div>
        <a href="{{ route('admin.pengajuan') }}" class="btn btn-navy px-3 py-2">Lihat Semua Pengajuan</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr class="small">
                    <th>Mahasiswa</th>
                    <th>Prodi</th>
                    <th>Instansi Tujuan</th>
                    <th>Periode PKL</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrean as $item)
                    <tr class="small">
                        <td>
                            <div class="fw-bold text-dark">{{ $item->nama_mahasiswa }}</div>
                            <span class="text-muted">{{ $item->nim }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $item->prodi }}</span></td>
                        <td class="fw-semibold">{{ $item->nama_perusahaan ?? '-' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} -
                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                        </td>
                        <td>
                            @if($item->status_verifikasi === 'pending')
                                <span class="badge rounded-pill" style="background: #FFF2D7; color: #A86D12;">Menunggu Verifikasi</span>
                            @elseif($item->status_verifikasi === 'revisi')
                                <span class="badge rounded-pill" style="background: #FCE9E8; color: #B94141;">Revisi</span>
                            @elseif($item->status_verifikasi === 'disetujui_admin')
                                <span class="badge rounded-pill" style="background: #E7F0FC; color: #346BA8;">Disetujui Admin</span>
                            @else
                                <span class="badge rounded-pill" style="background: #E5F4EA; color: #278052;">Aktif PKL</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pengajuan') }}?id={{ $item->id }}" class="btn btn-sm btn-outline-primary rounded-3">
                                Periksa Berkas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">Belum ada pengajuan PKL masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection