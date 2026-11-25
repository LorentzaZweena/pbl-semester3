<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin Prodi') - SIM-PKL TIK PNJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-mortarboard-fill"></i></div>
        <div>
            <div class="fw-bold lh-sm" style="font-size: 0.95rem;">Sistem Informasi<br>Magang TIK</div>
            <small style="font-size: 0.68rem; opacity: 0.7;">Panel Admin Program Studi</small>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        <a href="{{ route('admin.pengajuan') }}" class="nav-link-custom {{ request()->routeIs('admin.pengajuan*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check"></i> Verifikasi Pengajuan
        </a>
        <a href="{{ route('admin.dosen') }}" class="nav-link-custom {{ request()->routeIs('admin.dosen*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Kelola Dosbing
        </a>
        <a href="{{ route('admin.monitoring') }}" class="nav-link-custom {{ request()->routeIs('admin.monitoring*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data"></i> Dokumen & Nilai
        </a>
    </nav>

    <div class="sidebar-footer">
        <button type="button"
                class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 fw-semibold w-100"
                style="color: #F87171;"
                data-bs-toggle="modal"
                data-bs-target="#modalKonfirmasiLogout">
            <i class="bi bi-box-arrow-left fs-5"></i> Logout
        </button>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div class="text-muted small fw-medium">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="badge px-3 py-2 rounded-pill" style="background: var(--pale-blue); color: var(--navy);">
                <i class="bi bi-shield-check me-1"></i> Admin Prodi
            </span>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                     style="width: 38px; height: 38px; background: var(--navy); font-size: 0.85rem;">
                    AP
                </div>
                <div class="lh-1">
                    <div class="fw-bold small">{{ Auth::user()->name ?? 'Admin Prodi TIK' }}</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Jurusan TIK PNJ</small>
                </div>
            </div>
        </div>
    </header>

    <main class="content-area">
        @yield('content')
    </main>
</div>

<div class="modal fade" id="modalKonfirmasiLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 rounded-4 shadow text-center p-4">
            <div class="modal-body p-2">
                <h5 class="fw-bold mb-1">Konfirmasi Keluar</h5>
                <p class="text-muted small mb-4">Apakah Anda yakin ingin keluar dari panel Admin Prodi?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn text-white fw-semibold rounded-3" style="background: #278052;">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>