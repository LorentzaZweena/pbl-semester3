<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi Magang TIK PNJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="brand-badge">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color: #0F172A; letter-spacing: -0.3px;">Selamat datang!</h3>
        <p class="text-muted small mb-0">Silakan masuk ke Sistem Informasi Magang TIK</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success border-0 rounded-3 py-2 px-3 small mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="role" class="form-label-custom">Masuk Sebagai</label>
            <select id="role" name="role" class="form-select form-select-custom">
                <option value="mahasiswa" {{ old('role', 'mahasiswa') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                <option value="pembimbing_industri" {{ old('role') == 'pembimbing_industri' ? 'selected' : '' }}>Pembimbing Industri</option>
                <option value="admin_prodi" {{ old('role') == 'admin_prodi' ? 'selected' : '' }}>Admin Program Studi</option>
                <option value="kps" {{ old('role') == 'kps' ? 'selected' : '' }}>Koordinator Program Studi (KPS)</option>
                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label-custom">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control form-control-custom @error('email') is-invalid @enderror"
                   placeholder="Masukkan Email Anda"
                   required
                   autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label-custom">Password</label>
            <div class="password-wrapper">
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control form-control-custom pe-5 @error('password') is-invalid @enderror"
                       placeholder="Masukkan Password Anda"
                       required>
                <button type="button" class="btn-toggle-pass" onclick="togglePassword()" aria-label="Tampilkan Password">
                    <i class="bi bi-eye-slash-fill" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small fw-semibold text-dark" for="remember">
                    Ingatkan saya
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-login-submit">
                Masuk
            </button>
        </div>

        @if (Route::has('register'))
            <div class="text-center small text-muted">
                Mahasiswa belum punya akun?
                <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #17365D;">
                    Daftar Sekarang
                </a>
            </div>
        @endif
    </form>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
        }
    }
</script>
</body>
</html>