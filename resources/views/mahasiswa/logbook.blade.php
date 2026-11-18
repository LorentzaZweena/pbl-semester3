@extends('layouts.mahasiswa')

@section('title', 'Logbook Kegiatan PKL')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/logbook.css') }}">
@endpush

@section('content')
@if(session('success'))
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background: #E5F4EA; color: #278052; border-radius: 12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background: #FCE9E8; color: #B94141; border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

<div class="outer-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex gap-2">
            <button type="button" class="log-tab-btn active" id="tabHarianBtn" onclick="switchLogbookTab('harian')">
                Logbook Harian
            </button>
            <button type="button" class="log-tab-btn" id="tabMingguanBtn" onclick="switchLogbookTab('mingguan')">
                Logbook Mingguan
            </button>
        </div>

        <button type="button" class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#modalTambahLogbook" {{ !$pengajuanAktif ? 'disabled' : '' }}>
            <i class="bi bi-plus-lg me-1"></i> Tambah Kegiatan
        </button>
    </div>

    @if(!$pengajuanAktif)
        <div class="text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold">Belum Ada Pengajuan PKL Aktif</h6>
            <p class="text-muted small mb-3">Anda harus mengajukan PKL terlebih dahulu sebelum dapat mengisi logbook harian.</p>
            <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-navy">Buat Pengajuan PKL</a>
        </div>
    @else

    <div id="panelHarian">
        <div class="text-center mb-2">
            <h6 class="fw-bold mb-0" style="color: var(--navy);">
                {{ \Carbon\Carbon::parse($selectedWeekStart)->translatedFormat('F Y') }}
                <span class="badge bg-light text-dark border ms-1">Minggu ke-{{ $selectedWeek }}</span>
            </h6>
        </div>

        <div class="calendar-strip">
            <a href="{{ route('mahasiswa.logbook', ['week' => max(1, $selectedWeek - 1)]) }}" class="cal-nav-btn d-flex align-items-center text-decoration-none">
                <i class="bi bi-chevron-left"></i>
            </a>

            @foreach($weekDays as $day)
                @php
                    $hasLog = $logbooksHarian->where('tanggal', $day['date'])->count() > 0;
                @endphp
                <div class="cal-day-item {{ $hasLog ? 'has-entry' : '' }}" onclick="filterByDate('{{ $day['date'] }}', this)">
                    <div class="small fw-semibold day-name {{ $day['is_sunday'] ? 'text-danger' : 'text-muted' }}" style="font-size: 0.78rem;">
                        {{ $day['day_short'] }}
                    </div>
                    <div class="fw-bold day-num {{ $day['is_sunday'] ? 'text-danger' : 'text-dark' }}">
                        {{ $day['day_num'] }}
                    </div>
                </div>
            @endforeach

            <a href="{{ route('mahasiswa.logbook', ['week' => $selectedWeek + 1]) }}" class="cal-nav-btn d-flex align-items-center text-decoration-none border-start">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <small class="text-muted">Menampilkan catatan kegiatan <strong>Minggu ke-{{ $selectedWeek }}</strong></small>
            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 d-none" id="btnResetFilter" onclick="resetDateFilter()">
                Tampilkan Semua Hari Minggu Ini
            </button>
        </div>

        @forelse($logbooksHarian as $log)
            @php
                $parts = explode('||OUTPUT||', $log->uraian_kegiatan);
                $aktivitas = trim($parts[0] ?? $log->uraian_kegiatan);
                $output = trim($parts[1] ?? '-');
            @endphp
            <div class="log-entry-card daily-item" data-date="{{ $log->tanggal }}">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-2 mb-3 border-bottom">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h6 class="fw-bold mb-0" style="color: var(--navy);">
                            {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                        </h6>

                        @if($log->status_validasi == 'disetujui')
                            <span class="badge rounded-pill badge-valid px-3 py-1">
                                <i class="bi bi-check-circle me-1"></i> Tervalidasi Industri
                            </span>
                        @elseif($log->status_validasi == 'ditolak')
                            <span class="badge rounded-pill badge-revisi px-3 py-1">
                                <i class="bi bi-x-circle me-1"></i> Ditolak Industri
                            </span>
                        @else
                            <span class="badge rounded-pill badge-pending px-3 py-1">
                                <i class="bi bi-clock me-1"></i> Menunggu Industri
                            </span>
                        @endif

                        @if($log->status_persetujuan_dosen == 'disetujui')
                            <span class="badge rounded-pill px-3 py-1" style="background: #E7F0FC; color: #17365D; font-weight: 600;">
                                <i class="bi bi-patch-check-fill me-1"></i> Disetujui & TTD Dosen
                            </span>
                        @elseif($log->status_persetujuan_dosen == 'revisi')
                            <span class="badge rounded-pill badge-revisi px-3 py-1">
                                <i class="bi bi-pencil-square me-1"></i> Revisi Dosen
                            </span>
                        @else
                            <span class="badge rounded-pill bg-light text-muted border px-3 py-1">
                                Menunggu TTD Dosen
                            </span>
                        @endif
                    </div>

                    @if($log->dokumentasi_foto)
                        <a href="{{ asset('storage/' . $log->dokumentasi_foto) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3">
                            <i class="bi bi-image me-1"></i> Lihat Dokumentasi
                        </a>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="fw-bold small text-dark mb-1">Aktivitas</div>
                        <p class="small text-muted mb-0" style="white-space: pre-line;">{{ $aktivitas }}</p>
                    </div>
                    <div class="col-md-5">
                        <div class="fw-bold small text-dark mb-1">Hasil / Output</div>
                        <p class="small text-muted mb-0">{{ $output }}</p>
                    </div>
                </div>

                @if($log->catatan_pembimbing)
                    <div class="mt-3 pt-2 border-top small text-muted">
                        <strong class="text-dark"><i class="bi bi-chat-dots me-1"></i> Catatan Pembimbing:</strong> {{ $log->catatan_pembimbing }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="bi bi-journal-plus fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Belum Ada Kegiatan di Minggu ke-{{ $selectedWeek }}</h6>
                <p class="text-muted small mb-3">Klik tombol <strong>+ Tambah Kegiatan</strong> di pojok kanan atas untuk mencatat aktivitas harianmu.</p>
            </div>
        @endforelse
    </div>

    <div id="panelMingguan" class="d-none">
        <div class="text-center mb-2">
            <h6 class="fw-bold mb-0" style="color: var(--navy);">
                {{ \Carbon\Carbon::parse($selectedWeekStart)->translatedFormat('F Y') }}
            </h6>
        </div>

        <div class="week-selector-bar">
            <a href="{{ route('mahasiswa.logbook', ['week' => max(1, $selectedWeek - 1), 'tab' => 'mingguan']) }}" class="btn btn-sm btn-light border">
                <i class="bi bi-chevron-left"></i>
            </a>

            <div class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h6 class="fw-bold mb-0">Minggu ke-{{ $selectedWeek }}</h6>
                    @if($persenValidasi == 100 && $totalMingguIni > 0)
                        <span class="badge rounded-pill badge-valid px-3">Tervalidasi</span>
                    @else
                        <span class="badge rounded-pill badge-pending px-3">Dalam Proses</span>
                    @endif
                </div>
                <small class="text-muted">
                    ({{ \Carbon\Carbon::parse($selectedWeekStart)->translatedFormat('d M') }} -
                     {{ \Carbon\Carbon::parse($selectedWeekEnd)->translatedFormat('d M Y') }})
                </small>
            </div>

            <a href="{{ route('mahasiswa.logbook', ['week' => $selectedWeek + 1, 'tab' => 'mingguan']) }}" class="btn btn-sm btn-light border">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="weekly-box">
            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>Total Kegiatan (Target 5 Hari Kerja/Minggu)</span>
                    <span style="color: var(--navy);">{{ $persenKegiatan }}%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 10px; background: #D9E2ED;">
                    <div class="progress-bar" style="width: {{ $persenKegiatan }}%; background: var(--blue);"></div>
                </div>
                <small class="text-muted" style="font-size: 0.78rem;">{{ $totalMingguIni }} kegiatan tercatat minggu ini</small>
            </div>

            <div>
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>Status Validasi & Persetujuan Dosen</span>
                    <span style="color: var(--navy);">{{ $persenValidasi }}%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 10px; background: #D9E2ED;">
                    <div class="progress-bar" style="width: {{ $persenValidasi }}%; background: #278052;"></div>
                </div>
                <small class="text-muted" style="font-size: 0.78rem;">
                    {{ $jumlahTervalidasi }} tervalidasi, {{ max(0, $totalMingguIni - $jumlahTervalidasi) }} menunggu persetujuan
                </small>
            </div>
        </div>

        <div class="weekly-box">
            <h6 class="fw-bold pb-2 mb-3 border-bottom">Ringkasan Aktivitas</h6>
            @if($logbooksHarian->count() > 0)
                <ol class="small text-muted ps-3 mb-0 lh-lg">
                    @foreach($logbooksHarian as $item)
                        @php $judulAktivitas = explode('||OUTPUT||', $item->uraian_kegiatan)[0]; @endphp
                        <li><strong>[{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m') }}]</strong> {{ $judulAktivitas }}</li>
                    @endforeach
                </ol>
            @else
                <p class="small text-muted mb-0">Belum ada ringkasan aktivitas pada minggu ini.</p>
            @endif
        </div>

        <div class="weekly-box mb-0">
            <h6 class="fw-bold pb-2 mb-3 border-bottom">File Pendukung & Dokumentasi</h6>
            @php $filesMingguIni = $logbooksHarian->whereNotNull('dokumentasi_foto'); @endphp
            @if($filesMingguIni->count() > 0)
                <div class="row g-2">
                    @foreach($filesMingguIni as $fileLog)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-white border rounded-3">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="bi bi-file-earmark-image text-primary fs-4"></i>
                                    <div class="text-truncate">
                                        <div class="fw-semibold small text-truncate">{{ basename($fileLog->dokumentasi_foto) }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($fileLog->tanggal)->format('d M Y') }}</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $fileLog->dokumentasi_foto) }}" target="_blank" class="btn btn-sm btn-light border">
                                    Buka
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="small text-muted mb-0">Belum ada file dokumentasi yang diunggah pada minggu ini.</p>
            @endif
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="modalTambahLogbook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color: var(--navy);">Tambah Aktivitas Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('mahasiswa.logbook.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tanggal Kegiatan<span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Minggu Ke-<span class="text-danger">*</span></label>
                            <input type="number" name="minggu_ke" class="form-control rounded-3" min="1" max="24" value="{{ $selectedWeek }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Aktivitas<span class="text-danger">*</span></label>
                        <textarea name="aktivitas" rows="3" class="form-control rounded-3"
                                  placeholder="Contoh: Implementasi desain tampilan dashboard menggunakan Bootstrap 5..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Hasil / Output<span class="text-danger">*</span></label>
                        <textarea name="output" rows="2" class="form-control rounded-3"
                                  placeholder="Tuliskan hasil pekerjaan Anda (Contoh: Mockup UI / Halaman Login & Dashboard selesai)" required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small">Dokumentasi Foto / Bukti Kerja (Opsional)</label>
                        <input type="file" name="dokumentasi_foto" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted" style="font-size: 0.75rem;">Format JPG, PNG, atau PDF (Maks. 2 MB)</small>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-navy px-4">Simpan Aktivitas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'mingguan') {
            switchLogbookTab('mingguan');
        }
    });

    function switchLogbookTab(tab) {
        const isHarian = tab === 'harian';
        document.getElementById('panelHarian').classList.toggle('d-none', !isHarian);
        document.getElementById('panelMingguan').classList.toggle('d-none', isHarian);
        document.getElementById('tabHarianBtn').classList.toggle('active', isHarian);
        document.getElementById('tabMingguanBtn').classList.toggle('active', !isHarian);
    }

    function filterByDate(dateStr, el) {
        document.querySelectorAll('.cal-day-item').forEach(d => d.classList.remove('active'));
        el.classList.add('active');

        document.querySelectorAll('.daily-item').forEach(card => {
            card.classList.toggle('d-none', card.dataset.date !== dateStr);
        });
        document.getElementById('btnResetFilter').classList.remove('d-none');
    }

    function resetDateFilter() {
        document.querySelectorAll('.cal-day-item').forEach(d => d.classList.remove('active'));
        document.querySelectorAll('.daily-item').forEach(card => card.classList.remove('d-none'));
        document.getElementById('btnResetFilter').classList.add('d-none');
    }
</script>
@endpush