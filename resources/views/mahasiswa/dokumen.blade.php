@extends('layouts.mahasiswa')

@section('title', 'Dokumen PKL & Repository')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dokumen.css') }}">
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

<div class="outer-panel">
    <div class="mb-3">
        <h4 class="fw-bold mb-1" style="color: var(--text-dark);">Dokumen PKL</h4>
        <p class="text-muted small mb-0">Kelola dokumen persyaratan, laporan akhir, dan sinkronisasi repository</p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <button type="button" class="doc-tab-btn active" id="tabPersyaratanBtn" onclick="switchDocTab('persyaratan')">
            Dokumen Persyaratan
        </button>
        <button type="button" class="doc-tab-btn" id="tabRepositoryBtn" onclick="switchDocTab('repository')">
            Laporan dan Repository
        </button>
    </div>

    @if(!$pengajuanAktif)
        <div class="text-center py-5 border rounded-4 bg-light">
            <i class="bi bi-folder-x fs-1 text-muted d-block mb-2"></i>
            <h6 class="fw-bold">Belum Ada Pengajuan PKL Aktif</h6>
            <p class="text-muted small mb-3">Silakan ajukan PKL terlebih dahulu untuk mengakses pengelolaan dokumen F1–F7 dan repository.</p>
            <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-navy">Buat Pengajuan PKL</a>
        </div>
    @else

    <div id="panelPersyaratan">
        <div class="doc-list-wrapper">
            @foreach($daftarForm as $item)
                <div class="doc-row">
                    <div class="d-flex align-items-center gap-3">
                        <div class="doc-icon-box">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="color: var(--text-dark); font-size: 0.94rem;">
                                {{ $item['kode'] }} ({{ $item['nama'] }})
                            </div>
                            <small class="text-muted">{{ $item['keterangan'] }}</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        @if($item['status_type'] === 'approved')
                            <span class="badge rounded-pill badge-doc-approved px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ $item['status_label'] }}
                            </span>
                        @elseif($item['status_type'] === 'pending')
                            <span class="badge rounded-pill badge-doc-pending px-3 py-2">
                                <i class="bi bi-clock-fill me-1"></i> {{ $item['status_label'] }}
                            </span>
                        @elseif($item['status_type'] === 'locked')
                            <span class="badge rounded-pill badge-doc-locked px-3 py-2">
                                <i class="bi bi-lock-fill me-1"></i> {{ $item['status_label'] }}
                            </span>
                        @else
                            <span class="badge rounded-pill badge-doc-rejected px-3 py-2">
                                <i class="bi bi-x-circle-fill me-1"></i> {{ $item['status_label'] }}
                            </span>
                        @endif

                        <button type="button"
                                class="btn-detail"
                                onclick="openDetailModal(
                                    '{{ $item['kode'] }}',
                                    '{{ addslashes($item['nama']) }}',
                                    '{{ addslashes($item['deskripsi']) }}',
                                    '{{ $item['status_label'] }}',
                                    '{{ $item['status_type'] }}',
                                    '{{ $item['can_upload'] ? '1' : '0' }}',
                                    '{{ $item['field_name'] }}',
                                    '{{ $item['file_url'] }}'
                                )">
                            Lihat Detail
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="panelRepository" class="d-none">
        @php
            $hasLaporan = $dokumen && $dokumen->file_laporan_akhir_pdf;
            $isSynced = $dokumen && $dokumen->status_sinkronisasi_repo == 1;
        @endphp

        <div id="repoSavedView" class="{{ ($hasLaporan && $isSynced) ? '' : 'd-none' }}">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="repo-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">Laporan Akhir PKL</h6>
                            <span class="badge rounded-pill badge-doc-approved px-3 py-1">Tervalidasi</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 bg-white border rounded-3 mb-3">
                            <div class="d-flex align-items-center gap-3 text-truncate">
                                <div class="p-2 rounded-3" style="background: #FCE9E8; color: #B94141;">
                                    <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-bold small text-truncate">{{ $hasLaporan ? basename($dokumen->file_laporan_akhir_pdf) : 'Laporan_Akhir_PKL.pdf' }}</div>
                                    <small class="text-muted">Dokumen PDF Terunggah</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                @if($hasLaporan)
                                    <a href="{{ asset('storage/' . $dokumen->file_laporan_akhir_pdf) }}" target="_blank" class="btn btn-sm btn-light border" title="Unduh PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="toggleEditRepo(true)" title="Perbarui / Ganti Laporan">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white border rounded-3 overflow-hidden">
                            <table class="table table-sm mb-0 small">
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted" width="42%">Tanggal Unggah</td>
                                    <td class="p-2 px-3 fw-bold">
                                        {{ $dokumen && $dokumen->uploaded_at ? \Carbon\Carbon::parse($dokumen->uploaded_at)->translatedFormat('d F Y, H:i') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Status Repository</td>
                                    <td class="p-2 px-3">
                                        <span class="badge rounded-pill badge-doc-approved px-3">Tersimpan</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Link Repository</td>
                                    <td class="p-2 px-3">
                                        <a href="https://repository.pnj.ac.id" target="_blank" class="text-decoration-none fw-semibold" style="color: var(--blue);">
                                            repository.pnj.ac.id <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                @if($dokumen && $dokumen->link_video_demo)
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Video Demo</td>
                                    <td class="p-2 px-3">
                                        <a href="{{ $dokumen->link_video_demo }}" target="_blank" class="text-decoration-none">Tonton Video</a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="repo-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Metadata Repository</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleEditRepo(true)">
                                <i class="bi bi-pencil me-1"></i> Edit Metadata
                            </button>
                        </div>

                        <div class="bg-white border rounded-3 overflow-hidden">
                            <table class="table table-sm mb-0 small">
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted" width="35%">Judul</td>
                                    <td class="p-2 px-3 fw-bold">{{ $metadata['judul'] ?? 'Laporan Praktik Kerja Lapangan' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Kategori</td>
                                    <td class="p-2 px-3 fw-semibold">{{ $metadata['kategori'] ?? 'PKL' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Subkategori</td>
                                    <td class="p-2 px-3 fw-semibold">{{ $metadata['subkategori'] ?? ($mahasiswa->prodi ?? 'TI') }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Bahasa</td>
                                    <td class="p-2 px-3 fw-semibold">{{ $metadata['bahasa'] ?? 'Indonesia' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Kata Kunci</td>
                                    <td class="p-2 px-3 fw-semibold">{{ $metadata['kata_kunci'] ?? 'PKL, Sistem Informasi, PNJ' }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 px-3 fw-semibold text-muted">Status</td>
                                    <td class="p-2 px-3 fw-semibold">Private (Kampus)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="repoFormView" class="{{ ($hasLaporan && $isSynced) ? 'd-none' : '' }}">
            <form action="{{ route('mahasiswa.dokumen.repository') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="repo-card">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold mb-0">Laporan Akhir PKL</h6>
                                @if($hasLaporan)
                                    <span class="badge rounded-pill badge-doc-pending px-3">Draft Tersimpan</span>
                                @endif
                            </div>

                            <input type="file" name="file_laporan_akhir_pdf" id="laporanPdfInput" class="d-none" accept=".pdf">
                            <div class="dropzone-repo mb-3" onclick="document.getElementById('laporanPdfInput').click()">
                                <i class="bi bi-cloud-arrow-up fs-1" style="color: var(--navy);"></i>
                                <div class="fw-bold small mt-2" id="laporanFileLabel">
                                    {{ $hasLaporan ? 'File saat ini: ' . basename($dokumen->file_laporan_akhir_pdf) . ' (Klik untuk ganti)' : 'Seret dan lepaskan file di sini atau klik untuk memilih file' }}
                                </div>
                                <small class="text-muted">Format: PDF (Maks. 5 MB)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Sertifikat Industri (Opsional - PDF/JPG)</label>
                                <input type="file" name="file_sertifikat_industri" class="form-control form-control-sm rounded-3" accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold small">Link Video Demo Aplikasi (Opsional)</label>
                                <input type="url" name="link_video_demo" class="form-control form-control-sm rounded-3"
                                       placeholder="https://youtu.be/..." value="{{ $dokumen->link_video_demo ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="repo-card">
                            <h6 class="fw-bold mb-3">Metadata Repository</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Judul Laporan<span class="text-danger">*</span></label>
                                <input type="text" name="judul_laporan" class="form-control rounded-3"
                                       placeholder="Implementasi Sistem Informasi Magang TIK"
                                       value="{{ old('judul_laporan', $metadata['judul'] ?? '') }}" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Kategori</label>
                                    <input type="text" name="kategori" class="form-control rounded-3" value="{{ $metadata['kategori'] ?? 'PKL' }}" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Subkategori (Prodi)<span class="text-danger">*</span></label>
                                    <select name="subkategori" class="form-select rounded-3" required>
                                        <option value="TI" {{ ($metadata['subkategori'] ?? $mahasiswa->prodi) == 'TI' ? 'selected' : '' }}>TI</option>
                                        <option value="TMJ" {{ ($metadata['subkategori'] ?? $mahasiswa->prodi) == 'TMJ' ? 'selected' : '' }}>TMJ</option>
                                        <option value="TMD" {{ ($metadata['subkategori'] ?? $mahasiswa->prodi) == 'TMD' ? 'selected' : '' }}>TMD</option>
                                        <option value="TRK" {{ ($metadata['subkategori'] ?? $mahasiswa->prodi) == 'TRK' ? 'selected' : '' }}>TRK</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Bahasa</label>
                                <select name="bahasa" class="form-select rounded-3">
                                    <option value="Indonesia" {{ ($metadata['bahasa'] ?? '') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                    <option value="Inggris" {{ ($metadata['bahasa'] ?? '') == 'Inggris' ? 'selected' : '' }}>Inggris</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold small">Kata Kunci<span class="text-danger">*</span></label>
                                <input type="text" name="kata_kunci" class="form-control rounded-3"
                                       placeholder="SIM-PKL, Magang, Sistem Informasi"
                                       value="{{ old('kata_kunci', $metadata['kata_kunci'] ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    @if($hasLaporan && $isSynced)
                        <button type="button" class="btn btn-light border px-4 rounded-3" onclick="toggleEditRepo(false)">Batal</button>
                    @endif
                    <button type="submit" name="action_type" value="draft" class="btn btn-outline-secondary px-4 rounded-3">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action_type" value="submit" class="btn btn-navy px-4">
                        Kirim ke Repository
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="modalDetailDokumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold" id="modalDocTitle" style="color: var(--navy);">Detail Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="mb-3">
                    <span class="small text-muted d-block mb-1">Status Dokumen:</span>
                    <span id="modalDocStatusBadge" class="badge rounded-pill px-3 py-2">Status</span>
                </div>

                <div class="p-3 rounded-3 mb-3 small" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                    <div class="fw-bold mb-1">Keterangan / Persyaratan:</div>
                    <p class="text-muted mb-0" id="modalDocDesc">-</p>
                </div>

                <div id="modalExistingFileBox" class="d-none mb-3">
                    <a href="#" id="modalExistingFileLink" target="_blank" class="btn btn-outline-primary btn-sm w-100 rounded-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Lihat / Unduh Berkas Terunggah
                    </a>
                </div>

                <form id="modalUploadForm" action="{{ route('mahasiswa.dokumen.uploadForm') }}" method="POST" enctype="multipart/form-data" class="d-none">
                    @csrf
                    <input type="hidden" name="field_name" id="modalFieldName">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Unggah Berkas PDF (<span id="modalFieldLabel">F1</span>)<span class="text-danger">*</span></label>
                        <input type="file" name="dokumen_file" class="form-control rounded-3" accept=".pdf" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Format PDF (Maksimal 2 MB)</small>
                    </div>
                    <button type="submit" class="btn btn-navy w-100">
                        <i class="bi bi-upload me-1"></i> Unggah & Simpan Dokumen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('tab') === 'repository') {
            switchDocTab('repository');
        }
    });

    function switchDocTab(tab) {
        const isPersyaratan = tab === 'persyaratan';
        document.getElementById('panelPersyaratan').classList.toggle('d-none', !isPersyaratan);
        document.getElementById('panelRepository').classList.toggle('d-none', isPersyaratan);
        document.getElementById('tabPersyaratanBtn').classList.toggle('active', isPersyaratan);
        document.getElementById('tabRepositoryBtn').classList.toggle('active', !isPersyaratan);
    }

    function toggleEditRepo(showForm) {
        document.getElementById('repoSavedView').classList.toggle('d-none', showForm);
        document.getElementById('repoFormView').classList.toggle('d-none', !showForm);
    }

    const laporanInput = document.getElementById('laporanPdfInput');
    if (laporanInput) {
        laporanInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const sizeMB = (this.files[0].size / (1024 * 1024)).toFixed(2);
                document.getElementById('laporanFileLabel').textContent = `${this.files[0].name} (${sizeMB} MB)`;
            }
        });
    }

    function openDetailModal(kode, nama, desc, statusLabel, statusType, canUpload, fieldName, fileUrl) {
        document.getElementById('modalDocTitle').textContent = `${kode} - ${nama}`;
        document.getElementById('modalDocDesc').textContent = desc;

        const badge = document.getElementById('modalDocStatusBadge');
        badge.textContent = statusLabel;
        badge.className = 'badge rounded-pill px-3 py-2 ';
        if (statusType === 'approved') badge.className += 'badge-doc-approved';
        else if (statusType === 'pending') badge.className += 'badge-doc-pending';
        else if (statusType === 'locked') badge.className += 'badge-doc-locked';
        else badge.className += 'badge-doc-rejected';

        const existingBox = document.getElementById('modalExistingFileBox');
        if (fileUrl) {
            existingBox.classList.remove('d-none');
            document.getElementById('modalExistingFileLink').href = fileUrl;
        } else {
            existingBox.classList.add('d-none');
        }

        const uploadForm = document.getElementById('modalUploadForm');
        if (canUpload === '1') {
            uploadForm.classList.remove('d-none');
            document.getElementById('modalFieldName').value = fieldName;
            document.getElementById('modalFieldLabel').textContent = kode;
        } else {
            uploadForm.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('modalDetailDokumen')).show();
    }
</script>
@endpush