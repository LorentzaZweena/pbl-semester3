<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Mahasiswa') - SIM-PKL TIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mahasiswa.css') }}">
    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <div class="fw-bold lh-sm" style="font-size: 0.95rem;">Sistem Informasi<br>Magang TIK</div>
            <small style="font-size: 0.7rem; color: var(--soft-blue);">Bersama membangun masa depan</small>
        </div>
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link-custom {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-file-earmark-plus"></i> Pengajuan PKL
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-journal-text"></i> Logbook
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-people"></i> Bimbingan
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-folder2-open"></i> Dokumen
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-file-earmark-check"></i> Laporan
        </a>
        <a href="#" class="nav-link-custom">
            <i class="bi bi-person-circle"></i> Profil
        </a>
    </div>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 fw-semibold" style="color: #F87171;">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</aside>


<div class="main-wrapper">
    <header class="topbar">
        <button class="btn btn-light border d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="d-none d-lg-block text-muted small">
            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light btn-sm rounded-circle position-relative border" style="width: 36px; height: 36px;">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>
            <button class="btn btn-light btn-sm rounded-circle border" style="width: 36px; height: 36px;">
                <i class="bi bi-chat-left-text"></i>
            </button>

            <div class="d-flex align-items-center gap-2 ps-2 border-start">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->mahasiswa->nama_lengkap ?? 'Mahasiswa') }}&background=17365D&color=fff"
                     alt="Avatar" class="rounded-circle" width="38" height="38">
                <div class="lh-1">
                    <div class="fw-bold small">{{ Auth::user()->mahasiswa->nama_lengkap ?? 'Mahasiswa PNJ' }}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        Mahasiswa • {{ Auth::user()->mahasiswa->prodi ?? 'TI' }}
                    </small>
                </div>
            </div>
        </div>
    </header>

    <main class="content-area">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>