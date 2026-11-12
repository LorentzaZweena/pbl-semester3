<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-PKL TIK PNJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-wrapper">
    <div class="brand-header">
        <div class="brand-logo">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h4 class="fw-bold mb-0" style="color: var(--navy); letter-spacing: 0.5px;">SIM-PKL</h4>
        <small class="text-muted">Jurusan Teknik Informatika & Komputer</small>
    </div>

    <div class="login-card">
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-1">Selamat datang!</h5>
            <p class="text-muted small mb-0">Silakan masuk untuk melanjutkan</p>
        </div>

        @if (\$errors->any())
            <div class="alert py-2 px-3 small mb-3" style="background: #FCE9E8; color: #B94141; border-radius: 10px;">
                <i class="bi bi-exclamation-circle me-1"></i> {{ \$errors->first() }}
            </div>
        @endif

        <div class="role-switcher">
            <button type="button" class="role-btn active" id="tabMahasiswa" onclick="switchRole('mahasiswa')">
                Mahasiswa
            </button>
            <button type="button" class="role-btn" id="tabDosen" onclick="switchRole('dosen')">
                Dosen / Mitra
            </button>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="login_type" id="loginType" value="mahasiswa">

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text border-end-0 rounded-start-3">
                        <i class="bi bi-person" id="userIcon"></i>
                    </span>
                    <input type="text" name="email" id="identityInput"
                           class="form-control border-start-0 rounded-end-3"
                           placeholder="NIM / Email Mahasiswa" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text border-end-0 rounded-start-3">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="passwordInput"
                           class="form-control border-start-0 border-end-0"
                           placeholder="Password" required>
                    <button class="input-group-text border-start-0 rounded-end-3" type="button" onclick="togglePassword()">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted" for="remember">Ingatkan saya</label>
                </div>
                <a href="#" class="text-decoration-none fw-medium" style="color: var(--blue);">Lupa password?</a>
            </div>

            <button type="submit" class="btn btn-navy w-100">
                Masuk
            </button>
        </form>
    </div>
</div>

<script>
    function switchRole(role) {
        const tabMhs = document.getElementById('tabMahasiswa');
        const tabDsn = document.getElementById('tabDosen');
        const input = document.getElementById('identityInput');
        const loginType = document.getElementById('loginType');

        if (role === 'mahasiswa') {
            tabMhs.classList.add('active');
            tabDsn.classList.remove('active');
            input.placeholder = 'NIM / Email Mahasiswa';
            loginType.value = 'mahasiswa';
        } else {
            tabDsn.classList.add('active');
            tabMhs.classList.remove('active');
            input.placeholder = 'NIP / Email Dosen atau Mitra';
            loginType.value = 'staff';
        }
    }

    function togglePassword() {
        const pwd = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }
</script>
</body>
</html>