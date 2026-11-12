<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIM-PKL JTIK PNJ')</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; width: 260px; background-color: #1e293b; color: #fff; }
        .sidebar .nav-link { color: #cbd5e1; padding: 12px 20px; border-radius: 6px; margin: 2px 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0d6efd; color: #fff; }
        .content-wrapper { flex: 1; display: flex; flex-direction: column; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- SIDEBAR -->
    <aside class="sidebar d-flex flex-column p-2">
        <div class="p-3 border-bottom border-secondary mb-2">
            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-mortarboard-fill me-2"></i>SIM-PKL PNJ</h5>
            <small class="text-info text-uppercase">{{ str_replace('_', ' ', Auth::user()->role) }}</small>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            @php $role = Auth::user()->role; @endphp

            @if($role === 'super_admin')
                <li class="nav-item"><a href="{{ route('superadmin.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-people me-2"></i>Manajemen Akun</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-building me-2"></i>Data Instansi</a></li>
            
            @elseif($role === 'admin_prodi')
                <li class="nav-item"><a href="{{ route('adminprodi.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-check2-square me-2"></i>Verifikasi Pengajuan</a></li>
            
            @elseif($role === 'kps')
                <li class="nav-item"><a href="{{ route('kps.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-person-check me-2"></i>Penetapan Dosbing</a></li>
            
            @elseif($role === 'mahasiswa')
                <li class="nav-item"><a href="{{ route('mahasiswa.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-file-earmark-plus me-2"></i>Pengajuan PKL</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-journal-text me-2"></i>Logbook Harian (F3)</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-chat-dots me-2"></i>Bimbingan (F4)</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cloud-upload me-2"></i>Laporan Akhir</a></li>
            
            @elseif($role === 'dosen')
                <li class="nav-item"><a href="{{ route('dosen.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-people me-2"></i>Mahasiswa Bimbingan</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-award me-2"></i>Input Nilai (F6)</a></li>
            
            @elseif($role === 'pembimbing_industri')
                <li class="nav-item"><a href="{{ route('industri.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-list-check me-2"></i>Validasi F2 & Logbook</a></li>
                <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-star me-2"></i>Penilaian Industri (F5)</a></li>
            @endif
        </ul>

        <div class="border-top border-secondary pt-3 px-2">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="bi bi-box-arrow-left me-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="content-wrapper">
        <nav class="navbar navbar-light bg-white shadow-sm px-4 py-3">
            <span class="navbar-brand mb-0 h6">@yield('page-title', 'Dashboard')</span>
            <span class="text-muted small"><i class="bi bi-person-circle me-1"></i> {{ Auth::user()->email }}</span>
        </nav>

        <main class="p-4">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>