@extends('layouts.mahasiswa')

@section('title', 'Form Pengajuan PKL')

@push('styles')
<style>
    .wizard-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        max-width: 720px;
        margin: 0.75rem auto 0.25rem;
    }

    .wizard-stepper::before {
        content: "";
        position: absolute;
        top: 22px;
        left: 15%;
        right: 15%;
        height: 2px;
        background: var(--border-color);
        z-index: 1;
    }

    .wizard-step {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        cursor: pointer;
    }

    .wizard-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 2px solid var(--border-color);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-bottom: 0.5rem;
        transition: all 0.25s ease;
    }

    .wizard-step.active .wizard-circle {
        background: var(--pale-blue);
        border-color: var(--navy);
        color: var(--navy);
        box-shadow: 0 0 0 4px rgba(23, 54, 93, 0.12);
    }

    .wizard-step.completed .wizard-circle {
        background: var(--navy);
        border-color: var(--navy);
        color: #FFFFFF;
    }

    .wizard-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .wizard-step.active .wizard-label,
    .wizard-step.completed .wizard-label {
        color: var(--navy);
    }

    .section-outer {
        background: #E8EEF5;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.25rem;
    }

    .section-inner {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .form-label-custom {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--text-dark);
        margin-bottom: 0.4rem;
    }

    .form-control, .form-select {
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        padding: 0.65rem 0.9rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(49, 93, 139, 0.12);
    }

    .upload-dropzone {
        border: 2px dashed #B8C7D9;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        background: #F8FAFC;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .upload-dropzone:hover {
        border-color: var(--navy);
        background: var(--pale-blue);
    }

    .file-preview-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        background: #FFFFFF;
    }

    .btn-navy {
        background: var(--navy);
        color: #FFFFFF;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.65rem 1.5rem;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-navy:hover {
        background: var(--blue);
        color: #FFFFFF;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0" style="color: var(--navy);">Form Pengajuan PKL</h4>
    @if($pengajuanAktif)
        <span class="badge px-3 py-2 rounded-pill" style="background: #FFF2D7; color: #A86D12;">
            Status Saat Ini: {{ strtoupper(str_replace('_', ' ', $pengajuanAktif->status_verifikasi)) }}
        </span>
    @endif
</div>

@if(session('success'))
    <div class="alert border-0 mb-4 d-flex align-items-center gap-2" style="background: #E5F4EA; color: #278052; border-radius: 12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert border-0 mb-4" style="background: #FCE9E8; color: #B94141; border-radius: 12px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa kembali isian form:</div>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($pengajuanAktif && $pengajuanAktif->status_verifikasi === 'revisi')
    <div class="alert border-0 mb-4" style="background: #FCE9E8; color: #B94141; border-radius: 12px;">
        <div class="fw-bold"><i class="bi bi-info-circle-fill me-1"></i> Pengajuan Perlu Revisi:</div>
        <p class="mb-0 small mt-1">{{ $pengajuanAktif->catatan_admin ?? 'Silakan perbaiki dokumen atau data pengajuan Anda.' }}</p>
    </div>
@endif

<div class="section-outer mb-4">
    <h6 class="fw-bold mb-2" style="color: var(--navy);">Tahapan Pengajuan PKL</h6>
    <div class="wizard-stepper">
        <div class="wizard-step active" id="stepIndicator1" onclick="goToStep(1)">
            <div class="wizard-circle">1</div>
            <div class="wizard-label">Data Perusahaan</div>
        </div>
        <div class="wizard-step" id="stepIndicator2" onclick="goToStep(2)">
            <div class="wizard-circle">2</div>
            <div class="wizard-label">Pilih Dosen</div>
        </div>
        <div class="wizard-step" id="stepIndicator3" onclick="goToStep(3)">
            <div class="wizard-circle">3</div>
            <div class="wizard-label">Review</div>
        </div>
    </div>
</div>

<form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="formPengajuanPkl">
    @csrf
    <div id="stepContent1" class="section-outer mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--navy);">Pengajuan Magang</h5>
            <span class="badge bg-white text-dark border px-3 py-1 rounded-pill small">Langkah 1 dari 3</span>
        </div>

        <div class="section-inner">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label-custom">Nama Perusahaan<span class="text-danger">*</span></label>
                        <input type="text" name="nama_perusahaan" id="namaPerusahaan" class="form-control"
                               list="listPerusahaan" placeholder="Contoh: PT Telkom Indonesia"
                               value="{{ old('nama_perusahaan', $pengajuanAktif->instansi->nama_perusahaan ?? '') }}" required>
                        <datalist id="listPerusahaan">
                            @foreach($instansiList as $instansi)
                                <option value="{{ $instansi->nama_perusahaan }}">
                            @endforeach
                        </datalist>
                        <small class="text-muted" style="font-size: 0.75rem;">Pilih dari daftar mitra yang tersedia atau ketik nama perusahaan baru.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Bidang Magang<span class="text-danger">*</span></label>
                        <input type="text" name="bidang_industri" id="bidangMagang" class="form-control"
                               placeholder="Contoh: Teknik Informatika / Software Development"
                               value="{{ old('bidang_industri', $pengajuanAktif->instansi->bidang_industri ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Alamat Perusahaan<span class="text-danger">*</span></label>
                        <input type="text" name="alamat_perusahaan" id="alamatPerusahaan" class="form-control"
                               placeholder="Contoh: Jl. Sudirman Kav. 10, Jakarta"
                               value="{{ old('alamat_perusahaan', $pengajuanAktif->instansi->alamat ?? '') }}" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label-custom">Periode Magang<span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control"
                                       value="{{ old('tanggal_mulai', $pengajuanAktif->tanggal_mulai ?? '') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggalSelesai" class="form-control"
                                       value="{{ old('tanggal_selesai', $pengajuanAktif->tanggal_selesai ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3">
                            <label class="form-label-custom">Program Studi<span class="text-danger">*</span></label>
                            <select name="prodi" id="prodiSelect" class="form-select" required>
                                <option value="" disabled {{ !$mahasiswa ? 'selected' : '' }}>Pilih program studi</option>
                                <option value="TI" {{ ($mahasiswa->prodi ?? '') == 'TI' ? 'selected' : '' }}>Teknik Informatika (TI)</option>
                                <option value="TMJ" {{ ($mahasiswa->prodi ?? '') == 'TMJ' ? 'selected' : '' }}>Teknik Multimedia dan Jaringan (TMJ)</option>
                                <option value="TMD" {{ ($mahasiswa->prodi ?? '') == 'TMD' ? 'selected' : '' }}>Teknik Multimedia Digital (TMD)</option>
                                <option value="TRK" {{ ($mahasiswa->prodi ?? '') == 'TRK' ? 'selected' : '' }}>Teknik Rekayasa Komputer (TRK)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom">Bukti Penerimaan Perusahaan (LoA / Surat Balasan)<span class="text-danger">*</span></label>
                            <input type="file" name="file_bukti_keterimaan" id="fileInput" class="d-none" accept=".pdf">

                            <div class="upload-dropzone mb-2" id="dropzoneArea" onclick="document.getElementById('fileInput').click()">
                                <i class="bi bi-cloud-arrow-up fs-2" style="color: var(--navy);"></i>
                                <div class="fw-semibold small mt-1">Klik untuk unggah surat penerimaan</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Format PDF (Maksimal 2 MB)</small>
                            </div>

                            <div class="file-preview-box d-none" id="filePreviewBox">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-3" style="background: #FCE9E8; color: #B94141;">
                                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark" id="fileNameDisplay">Surat_penerimaan.pdf</div>
                                        <small class="text-muted" id="fileSizeDisplay">1.2 MB</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeFile()" title="Hapus file">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-navy" onclick="goToStep(2)">
                            Lanjut Pilih Dosen <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="stepContent2" class="section-outer mb-4 d-none">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--navy);">Pengajuan Dosen Pembimbing Kampus</h5>
            <span class="badge bg-white text-dark border px-3 py-1 rounded-pill small">Langkah 2 dari 3</span>
        </div>

        <div class="section-inner">
            <div class="alert py-2 px-3 small mb-4" style="background: #E7F0FC; color: #346BA8; border-radius: 10px;">
                <i class="bi bi-info-circle-fill me-1"></i>
                Pilih <strong>3 kandidat dosen pembimbing</strong> sesuai prioritas Anda. Admin Prodi / KPS akan menetapkan 1 dosen pembimbing berdasarkan kuota yang tersedia.
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Pilihan dosen pertama<span class="text-danger">*</span></label>
                <select name="opsi_dosbing_1" id="dosbing1" class="form-select dosbing-select" required>
                    <option value="">-- Pilih Dosen Pembimbing Prioritas 1 --</option>
                    @foreach($dosenList as $dosen)
                        @php $sisaKuota = $dosen->kuota_maksimal - $dosen->kuota_terpakai; @endphp
                        <option value="{{ $dosen->nip }}" data-nama="{{ $dosen->nama_lengkap }}" {{ $sisaKuota <= 0 ? 'disabled' : '' }}>
                            {{ $dosen->nama_lengkap }} (NIP: {{ $dosen->nip }} • Sisa Kuota: {{ $sisaKuota }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Pilihan dosen kedua<span class="text-danger">*</span></label>
                <select name="opsi_dosbing_2" id="dosbing2" class="form-select dosbing-select" required>
                    <option value="">-- Pilih Dosen Pembimbing Prioritas 2 --</option>
                    @foreach($dosenList as $dosen)
                        @php $sisaKuota = $dosen->kuota_maksimal - $dosen->kuota_terpakai; @endphp
                        <option value="{{ $dosen->nip }}" data-nama="{{ $dosen->nama_lengkap }}" {{ $sisaKuota <= 0 ? 'disabled' : '' }}>
                            {{ $dosen->nama_lengkap }} (NIP: {{ $dosen->nip }} • Sisa Kuota: {{ $sisaKuota }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label-custom">Pilihan dosen ketiga<span class="text-danger">*</span></label>
                <select name="opsi_dosbing_3" id="dosbing3" class="form-select dosbing-select" required>
                    <option value="">-- Pilih Dosen Pembimbing Prioritas 3 --</option>
                    @foreach($dosenList as $dosen)
                        @php $sisaKuota = $dosen->kuota_maksimal - $dosen->kuota_terpakai; @endphp
                        <option value="{{ $dosen->nip }}" data-nama="{{ $dosen->nama_lengkap }}" {{ $sisaKuota <= 0 ? 'disabled' : '' }}>
                            {{ $dosen->nama_lengkap }} (NIP: {{ $dosen->nip }} • Sisa Kuota: {{ $sisaKuota }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" onclick="goToStep(1)">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-navy" onclick="goToStep(3)">
                    Review Pengajuan <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="stepContent3" class="section-outer mb-4 d-none">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--navy);">Review Data Pengajuan PKL</h5>
            <span class="badge bg-white text-dark border px-3 py-1 rounded-pill small">Langkah 3 dari 3</span>
        </div>

        <div class="section-inner">
            <p class="text-muted small mb-4">Pastikan seluruh data perusahaan, periode magang, berkas PDF, dan pilihan dosen pembimbing sudah benar sebelum dikirim untuk diverifikasi Admin Prodi.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                        <div class="fw-bold small text-uppercase mb-2" style="color: var(--navy);">Informasi Instansi & Periode</div>
                        <table class="table table-sm table-borderless small mb-0">
                            <tr><td class="text-muted" width="40%">Perusahaan</td><td class="fw-bold" id="revPerusahaan">-</td></tr>
                            <tr><td class="text-muted">Bidang</td><td class="fw-semibold" id="revBidang">-</td></tr>
                            <tr><td class="text-muted">Alamat</td><td class="fw-semibold" id="revAlamat">-</td></tr>
                            <tr><td class="text-muted">Periode</td><td class="fw-semibold" id="revPeriode">-</td></tr>
                            <tr><td class="text-muted">File Bukti</td><td class="fw-bold text-success" id="revFile">-</td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100" style="background: var(--bg-main); border: 1px solid var(--border-color);">
                        <div class="fw-bold small text-uppercase mb-2" style="color: var(--navy);">Kandidat Dosen Pembimbing</div>
                        <ol class="small fw-semibold mb-0 ps-3 lh-lg">
                            <li id="revDosbing1">-</li>
                            <li id="revDosbing2">-</li>
                            <li id="revDosbing3">-</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3" onclick="goToStep(2)">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </button>
                <button type="submit" class="btn btn-navy px-4">
                    <i class="bi bi-send-fill me-1"></i> Kirim Pengajuan PKL
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function goToStep(step) {
        [1, 2, 3].forEach(num => {
            document.getElementById('stepContent' + num).classList.toggle('d-none', num !== step);
            const indicator = document.getElementById('stepIndicator' + num);
            indicator.classList.remove('active', 'completed');
            if (num < step) indicator.classList.add('completed');
            if (num === step) indicator.classList.add('active');
        });

        if (step === 3) {
            document.getElementById('revPerusahaan').textContent = document.getElementById('namaPerusahaan').value || '-';
            document.getElementById('revBidang').textContent = document.getElementById('bidangMagang').value || '-';
            document.getElementById('revAlamat').textContent = document.getElementById('alamatPerusahaan').value || '-';

            const tglMulai = document.getElementById('tanggalMulai').value;
            const tglSelesai = document.getElementById('tanggalSelesai').value;
            document.getElementById('revPeriode').textContent = (tglMulai && tglSelesai) ? `${tglMulai} s/d ${tglSelesai}` : '-';

            const fileInput = document.getElementById('fileInput');
            document.getElementById('revFile').textContent = fileInput.files.length ? fileInput.files[0].name : 'Belum diunggah';

            const d1 = document.getElementById('dosbing1');
            const d2 = document.getElementById('dosbing2');
            const d3 = document.getElementById('dosbing3');
            document.getElementById('revDosbing1').textContent = d1.selectedIndex > 0 ? d1.options[d1.selectedIndex].dataset.nama : '-';
            document.getElementById('revDosbing2').textContent = d2.selectedIndex > 0 ? d2.options[d2.selectedIndex].dataset.nama : '-';
            document.getElementById('revDosbing3').textContent = d3.selectedIndex > 0 ? d3.options[d3.selectedIndex].dataset.nama : '-';
        }
    }

    const fileInput = document.getElementById('fileInput');
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById('fileNameDisplay').textContent = file.name;
            document.getElementById('fileSizeDisplay').textContent = sizeMB + ' MB';
            document.getElementById('dropzoneArea').classList.add('d-none');
            document.getElementById('filePreviewBox').classList.remove('d-none');
        }
    });

    function removeFile() {
        fileInput.value = '';
        document.getElementById('filePreviewBox').classList.add('d-none');
        document.getElementById('dropzoneArea').classList.remove('d-none');
    }

    const dosbingSelects = document.querySelectorAll('.dosbing-select');
    dosbingSelects.forEach(select => {
        select.addEventListener('change', () => {
            const selectedValues = Array.from(dosbingSelects).map(s => s.value).filter(v => v !== '');
            dosbingSelects.forEach(s => {
                Array.from(s.options).forEach(opt => {
                    if (opt.value !== '' && opt.value !== s.value) {
                        opt.hidden = selectedValues.includes(opt.value);
                    } else {
                        opt.hidden = false;
                    }
                });
            });
        });
    });
</script>
@endpush