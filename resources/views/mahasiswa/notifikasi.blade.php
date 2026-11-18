@extends('layouts.mahasiswa')

@section('title', 'Notifikasi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notifikasi.css') }}">
@endpush

@section('content')
@if(session('success'))
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background: #E5F4EA; color: #278052; border-radius: 12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<div class="outer-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--text-dark);">Notifikasi</h4>
            <p class="text-muted small mb-0">Pembaruan status pengajuan PKL, validasi logbook, bimbingan, dan penilaian</p>
        </div>

        @if(count($notifications) > 0)
            <form action="{{ route('mahasiswa.notifikasi.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <button type="button" class="filter-pill-btn active" onclick="filterNotif('all', this)">
            Semua ({{ count($notifications) }})
        </button>
        <button type="button" class="filter-pill-btn" onclick="filterNotif('pengajuan', this)">
            Pengajuan PKL
        </button>
        <button type="button" class="filter-pill-btn" onclick="filterNotif('logbook', this)">
            Logbook
        </button>
        <button type="button" class="filter-pill-btn" onclick="filterNotif('bimbingan_nilai', this)">
            Bimbingan & Penilaian
        </button>
    </div>

    <div class="notif-list-box">
        @forelse($notifications as $notif)
            <div class="notif-row {{ $notif['is_unread'] ? 'unread' : '' }} notif-item" data-category="{{ $notif['category'] }}">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div class="notif-icon" style="background: {{ $notif['bg_icon'] }}; color: {{ $notif['color_icon'] }};">
                        <i class="bi {{ $notif['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="notif-title {{ $notif['title_class'] }}">{{ $notif['title'] }}</span>
                            @if($notif['is_unread'])
                                <span class="badge rounded-pill" style="background: var(--navy); font-size: 0.65rem;">Baru</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-1">{{ Str::limit($notif['message'], 90) }}</p>
                        <small class="text-muted d-md-none" style="font-size: 0.78rem;">
                            <i class="bi bi-calendar3 me-1"></i> {{ $notif['date_formatted'] }}
                        </small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-4 ms-auto">
                    <span class="small text-muted d-none d-md-inline" style="min-width: 115px; text-align: right;">
                        {{ $notif['date_formatted'] }}
                    </span>

                    <button type="button"
                            class="btn-lihat-detail"
                            onclick="openNotifModal(
                                '{{ addslashes($notif['title']) }}',
                                '{{ $notif['date_formatted'] }}',
                                '{{ addslashes($notif['message']) }}',
                                '{{ $notif['action_url'] }}',
                                '{{ $notif['action_label'] }}'
                            )">
                        Lihat Detail
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted d-block mb-2"></i>
                <h6 class="fw-bold">Belum Ada Notifikasi</h6>
                <p class="text-muted small mb-0">Semua pembaruan terkait pengajuan PKL, logbook, dan bimbingan akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="modalDetailNotif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main); border-radius: 16px 16px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalNotifTitle" style="color: var(--navy);">Detail Notifikasi</h5>
                    <small class="text-muted" id="modalNotifDate">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 rounded-3 small lh-lg" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-dark);" id="modalNotifMessage">
                    -
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="modalNotifActionBtn" class="btn text-white fw-semibold px-4 rounded-3" style="background: var(--navy);">
                    Buka Halaman Terkait
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterNotif(category, btn) {
        document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.notif-item').forEach(row => {
            if (category === 'all' || row.dataset.category === category) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });
    }

    function openNotifModal(title, date, message, actionUrl, actionLabel) {
        document.getElementById('modalNotifTitle').textContent = title;
        document.getElementById('modalNotifDate').textContent = 'Tanggal: ' + date;
        document.getElementById('modalNotifMessage').textContent = message;

        const actionBtn = document.getElementById('modalNotifActionBtn');
        actionBtn.href = actionUrl;
        actionBtn.textContent = actionLabel;

        new bootstrap.Modal(document.getElementById('modalDetailNotif')).show();
    }
</script>
@endpush