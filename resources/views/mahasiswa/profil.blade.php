@extends('layouts.mahasiswa')

@section('title', 'Profil Mahasiswa')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
@endpush

@section('content')
@if(session('success'))
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background: #E5F4EA; color: #278052; border-radius: 12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert border-0 mb-3" style="background: #FCE9E8; color: #B94141; border-radius: 12px;">
        <ul class="mb-0 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $prodiFull = match($mahasiswa->prodi ?? '') {
        'TI'  => 'Teknik Informatika',
        'TMJ' => 'Teknik Multimedia dan Jaringan',
        'TMD' => 'Teknik Multimedia Digital',
        'TC' => 'Teknik Cyber',
        default => 'Jurusan Teknik Informatika & Komputer',
    };
@endphp

<div class="outer-panel">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-4 mb-4 border-bottom">
        <div class="d-flex align-items-center gap-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($mahasiswa->nama_lengkap ?? 'Mahasiswa') }}&background=17365D&color=fff&size=128"
                 alt="Foto Profil" class="profile-avatar">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $mahasiswa->nama_lengkap ?? 'Mahasiswa PNJ' }}</h4>
                <div class="fw-semibold text-muted mb-1">{{ $mahasiswa->nim ?? '-' }}</div>
                <div class="small text-muted">{{ $prodiFull }} • Kelas {{ $mahasiswa->kelas ?? '-' }}</div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-3 px-3 small fw-semibold" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                <i class="bi bi-shield-lock me-1"></i> Ubah Password
            </button>
            <button type="button" class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#modalEditProfil">
                <i class="bi bi-pencil-square me-1"></i> Edit Profil
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="info-card">
                <div class="info-card-title">
                    <span>Informasi Pribadi</span>
                    <i class="bi bi-person-vcard text-muted"></i>
                </div>
                <table class="table table-borderless info-table mb-0">
                    <tr>
                        <td class="info-label">Email</td>
                        <td class="info-value">: {{ Auth::user()->email }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">No. HP</td>
                        <td class="info-value">: {{ $mahasiswa->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat</td>
                        <td class="info-value">: {{ $extraProfile['alamat'] }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="info-card">
                <div class="info-card-title">
                    <span>Informasi Akademik</span>
                    <i class="bi bi-mortarboard text-muted"></i>
                </div>
                <table class="table table-borderless info-table mb-0">
                    <tr>
                        <td class="info-label">Angkatan</td>
                        <td class="info-value">: {{ $mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Semester / Kelas</td>
                        <td class="info-value">: Semester {{ $extraProfile['semester'] }} ({{ $mahasiswa->kelas ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="info-label">IPK</td>
                        <td class="info-value">: {{ $extraProfile['ipk'] }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="info-card">
                <div class="info-card-title">
                    <span>Informasi Magang</span>
                    <i class="bi bi-briefcase text-muted"></i>
                </div>
                <table class="table table-borderless info-table mb-0">
                    <tr>
                        <td class="info-label">PT / Instansi</td>
                        <td class="info-value fw-bold" style="color: #7A2E2E;">
                            : {{ $pengajuan->nama_perusahaan ?? 'Belum Terdaftar' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Bidang Magang</td>
                        <td class="info-value">: {{ $pengajuan->bidang_industri ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Periode Magang</td>
                        <td class="info-value">
                            : @if($pengajuan)
                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d F Y') }} -
                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d F Y') }}
                              @else
                                -
                              @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Dosen Pembimbing</td>
                        <td class="info-value">: {{ $pengajuan->nama_dosbing ?? 'Belum Ditetapkan' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label align-middle">Status Magang</td>
                        <td class="info-value">
                            : @if($pengajuan && $pengajuan->status_verifikasi === 'aktif_pkl')
                                <span class="badge-status-aktif">Sedang Berlangsung</span>
                              @elseif($pengajuan)
                                <span class="badge-status-pending text-capitalize">{{ str_replace('_', ' ', $pengajuan->status_verifikasi) }}</span>
                              @else
                                <span class="badge bg-light text-muted border">Belum Mengajukan</span>
                              @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="info-card">
                <div class="info-card-title">
                    <span>Keahlian / Kompetensi</span>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold" style="color: var(--blue);" data-bs-toggle="modal" data-bs-target="#modalEditProfil">
                        + Kelola
                    </button>
                </div>

                <div class="row g-2 mt-1">
                    @forelse($extraProfile['skills'] as $skill)
                        <div class="col-6">
                            <div class="skill-pill">{{ trim($skill) }}</div>
                        </div>
                    @empty
                        <div class="col-12 text-muted small">Belum ada keahlian ditambahkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color: var(--navy);">Edit Profil Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('mahasiswa.profil.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nomor HP (WhatsApp)<span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control rounded-3" value="{{ $mahasiswa->no_hp ?? '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" class="form-control rounded-3">{{ $extraProfile['alamat'] }}</textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Semester Saat Ini</label>
                            <input type="number" name="semester" min="1" max="14" class="form-control rounded-3" value="{{ $extraProfile['semester'] }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">IPK Terakhir</label>
                            <input type="text" name="ipk" class="form-control rounded-3" value="{{ $extraProfile['ipk'] }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small">Keahlian / Kompetensi (Pisahkan dengan koma)</label>
                        <input type="text" name="skills" class="form-control rounded-3"
                               placeholder="Contoh: UI/UX Design, Web Development, Figma, JavaScript"
                               value="{{ implode(', ', $extraProfile['skills']) }}">
                        <small class="text-muted" style="font-size: 0.75rem;">Gunakan tanda koma (,) untuk memisahkan setiap keahlian.</small>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-navy px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGantiPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color: var(--navy);">Ubah Password Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('mahasiswa.profil.password') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password Saat Ini<span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password Baru<span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3" minlength="6" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Konfirmasi Password Baru<span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" minlength="6" required>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-navy px-4">Perbarui Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection