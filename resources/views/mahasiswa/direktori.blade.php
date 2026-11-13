@extends('layouts.mahasiswa')

@section('title', 'Pilih Dosen & PT/Instansi')

@push('styles')
<style>
    .outer-panel {
        background: #E8EEF5;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .dir-tab-btn {
        padding: 0.6rem 1.75rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1.5px solid var(--border-color);
        background: #FFFFFF;
        color: var(--text-dark);
        transition: all 0.2s ease;
    }

    .dir-tab-btn.active {
        background: var(--navy);
        border-color: var(--navy);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(23, 54, 93, 0.2);
    }

    .search-input-group .form-control,
    .search-input-group .input-group-text,
    .filter-select {
        border: 1.5px solid var(--border-color);
        background: #FFFFFF;
        font-size: 0.9rem;
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }

    .search-input-group .input-group-text {
        border-right: 0;
        border-radius: 10px 0 0 10px;
        color: var(--text-muted);
    }

    .search-input-group .form-control {
        border-left: 0;
        border-radius: 0 10px 10px 0;
    }

    .filter-select {
        border-radius: 10px;
    }

    .list-container-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.5rem 1.5rem;
    }

    .directory-item {
        padding: 1.25rem 0;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s ease;
    }

    .directory-item:last-child {
        border-bottom: none;
    }

    .avatar-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--subtle);
        border: 1.5px solid var(--border-color);
        color: var(--navy);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .btn-pilih {
        background: #6B9E78;
        color: #FFFFFF;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.45rem 1.35rem;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
        min-width: 110px;
    }

    .btn-pilih:hover {
        background: #278052;
        color: #FFFFFF;
        transform: translateY(-1px);
    }

    .btn-pilih.selected {
        background: var(--navy);
        color: #FFFFFF;
    }

    .btn-full {
        background: transparent;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.45rem 0.75rem;
        border: none;
        cursor: not-allowed;
        min-width: 110px;
    }

    .selection-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 950;
        background: var(--navy);
        color: #FFFFFF;
        border-radius: 14px;
        padding: 1rem 1.5rem;
        box-shadow: 0 12px 28px rgba(23, 54, 93, 0.35);
        margin-top: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="outer-panel">
    <div class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="dir-tab-btn active" id="btnTabDosen" onclick="switchDirectoryTab('dosen')">
            Pilih Dosen
        </button>
        <button type="button" class="dir-tab-btn" id="btnTabPt" onclick="switchDirectoryTab('pt')">
            Pilih PT/Instansi
        </button>
    </div>

    <div id="panelDosen">
        <h4 class="fw-bold mb-3" style="color: var(--text-dark);">Pilih Dosen Pembimbing</h4>

        <div class="row g-2 mb-3">
            <div class="col-md-8">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchDosen" class="form-control" placeholder="Cari nama dosen atau NIP..." onkeyup="filterDosen()">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filterProdiDosen" class="form-select filter-select" onchange="filterDosen()">
                    <option value="">Semua Prodi Homebase</option>
                    <option value="TI">Teknik Informatika (TI)</option>
                    <option value="TMJ">Teknik Multimedia & Jaringan (TMJ)</option>
                    <option value="TMD">Teknik Multimedia Digital (TMD)</option>
                    <option value="TRK">Teknik Rekayasa Komputer (TRK)</option>
                </select>
            </div>
        </div>

        <div class="list-container-card" id="dosenListContainer">
            @forelse($dosenList as $dosen)
                @php
                    $sisaSlot = $dosen->kuota_maksimal - $dosen->kuota_terpakai;
                    $isFull = $sisaSlot <= 0;
                @endphp
                <div class="directory-item dosen-card"
                     data-nama="{{ strtolower($dosen->nama_lengkap . ' ' . $dosen->nip) }}"
                     data-prodi="{{ $dosen->prodi_homebase }}">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $dosen->nama_lengkap }}</h5>
                                    <table class="table table-borderless table-sm mb-0 small text-muted" style="line-height: 1.4;">
                                        <tr>
                                            <td class="p-0 pe-3" width="110">NIP</td>
                                            <td class="p-0">: {{ $dosen->nip }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-0 pe-3">Homebase</td>
                                            <td class="p-0">: Prodi {{ $dosen->prodi_homebase }} — Jurusan TIK PNJ</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="d-flex align-items-center justify-content-lg-end justify-content-between gap-4">
                                <div class="text-start" style="min-width: 115px;">
                                    <div class="fw-bold fs-5 lh-1" style="color: var(--text-dark);">
                                        {{ $dosen->kuota_terpakai }}/{{ $dosen->kuota_maksimal }}
                                    </div>
                                    @if($isFull)
                                        <small class="fw-semibold" style="color: #B94141;">slot penuh</small>
                                    @else
                                        <small class="fw-semibold" style="color: #278052;">{{ $sisaSlot }} slot tersedia</small>
                                    @endif
                                </div>

                                @if($isFull)
                                    <button type="button" class="btn-full" disabled>Tidak Tersedia</button>
                                @else
                                    <button type="button"
                                            class="btn-pilih btn-pilih-dosen"
                                            id="btnDosen_{{ $dosen->nip }}"
                                            onclick="togglePilihDosen('{{ $dosen->nip }}', '{{ addslashes($dosen->nama_lengkap) }}')">
                                        Pilih
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    Belum ada data dosen pembimbing tersedia.
                </div>
            @endforelse
        </div>
    </div>

    <div id="panelPt" class="d-none">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);">Pilih PT / Instansi Mitra</h4>
            <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-sm bg-white border fw-semibold" style="color: var(--navy); border-radius: 8px;">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan PT Baru
            </a>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-8">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchPt" class="form-control" placeholder="Cari nama perusahaan atau alamat..." onkeyup="filterPt()">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filterBidangPt" class="form-select filter-select" onchange="filterPt()">
                    <option value="">Semua Bidang Industri</option>
                    @foreach($bidangList as $bidang)
                        <option value="{{ strtolower($bidang) }}">{{ $bidang }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="list-container-card">
            @forelse($instansiList as $instansi)
                <div class="directory-item pt-card"
                     data-nama="{{ strtolower($instansi->nama_perusahaan . ' ' . $instansi->alamat) }}"
                     data-bidang="{{ strtolower($instansi->bidang_industri ?? '') }}">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle rounded-3">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $instansi->nama_perusahaan }}</h5>
                                    <table class="table table-borderless table-sm mb-0 small text-muted" style="line-height: 1.4;">
                                        <tr>
                                            <td class="p-0 pe-3" width="90">Bidang</td>
                                            <td class="p-0">: {{ $instansi->bidang_industri ?? 'Teknologi Informasi' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="p-0 pe-3">Alamat</td>
                                            <td class="p-0">: {{ $instansi->alamat }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-lg-end justify-content-between gap-3">
                                <span class="badge px-3 py-2 rounded-pill" style="background: #E5F4EA; color: #278052;">
                                    Mitra Terdaftar
                                </span>
                                <button type="button"
                                        class="btn-pilih btn-pilih-pt"
                                        id="btnPt_{{ $instansi->id }}"
                                        onclick="pilihPt('{{ $instansi->id }}', '{{ addslashes($instansi->nama_perusahaan) }}')">
                                    Pilih
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                    Belum ada data instansi mitra. Anda dapat menambahkannya langsung di Form Pengajuan PKL.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="selection-bar d-none" id="selectionBar">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="small text-white-50 mb-1">Ringkasan Pilihan Anda (Maks. 3 Dosen & 1 PT):</div>
            <div class="d-flex flex-wrap gap-2 align-items-center" id="selectedBadges"></div>
        </div>
        <a href="{{ route('mahasiswa.pengajuan') }}" id="btnLanjutPengajuan" class="btn btn-light fw-bold px-4" style="color: var(--navy); border-radius: 10px;">
            Gunakan di Form Pengajuan <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedDosen = []; 
    let selectedPt = null;

    function switchDirectoryTab(tab) {
        const isDosen = tab === 'dosen';
        document.getElementById('panelDosen').classList.toggle('d-none', !isDosen);
        document.getElementById('panelPt').classList.toggle('d-none', isDosen);
        document.getElementById('btnTabDosen').classList.toggle('active', isDosen);
        document.getElementById('btnTabPt').classList.toggle('active', !isDosen);
    }

    function filterDosen() {
        const keyword = document.getElementById('searchDosen').value.toLowerCase();
        const prodi = document.getElementById('filterProdiDosen').value;
        document.querySelectorAll('.dosen-card').forEach(card => {
            const matchNama = card.dataset.nama.includes(keyword);
            const matchProdi = !prodi || card.dataset.prodi === prodi;
            card.classList.toggle('d-none', !(matchNama && matchProdi));
        });
    }

    function filterPt() {
        const keyword = document.getElementById('searchPt').value.toLowerCase();
        const bidang = document.getElementById('filterBidangPt').value;
        document.querySelectorAll('.pt-card').forEach(card => {
            const matchNama = card.dataset.nama.includes(keyword);
            const matchBidang = !bidang || card.dataset.bidang.includes(bidang);
            card.classList.toggle('d-none', !(matchNama && matchBidang));
        });
    }

    function togglePilihDosen(nip, nama) {
        const idx = selectedDosen.findIndex(d => d.nip === nip);
        const btn = document.getElementById('btnDosen_' + nip);

        if (idx > -1) {
            selectedDosen.splice(idx, 1);
            btn.classList.remove('selected');
            btn.textContent = 'Pilih';
        } else {
            if (selectedDosen.length >= 3) {
                alert('Anda sudah memilih maksimal 3 kandidat dosen pembimbing.');
                return;
            }
            selectedDosen.push({ nip, nama });
            btn.classList.add('selected');
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Terpilih (' + selectedDosen.length + ')';
        }
        updateSelectionBar();
    }

    function pilihPt(id, nama) {
        document.querySelectorAll('.btn-pilih-pt').forEach(b => {
            b.classList.remove('selected');
            b.textContent = 'Pilih';
        });

        if (selectedPt && selectedPt.id === id) {
            selectedPt = null;
        } else {
            selectedPt = { id, nama };
            const btn = document.getElementById('btnPt_' + id);
            btn.classList.add('selected');
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Terpilih';
        }
        updateSelectionBar();
    }

    function updateSelectionBar() {
        const bar = document.getElementById('selectionBar');
        const container = document.getElementById('selectedBadges');
        const linkBtn = document.getElementById('btnLanjutPengajuan');

        if (selectedDosen.length === 0 && !selectedPt) {
            bar.classList.add('d-none');
            return;
        }

        bar.classList.remove('d-none');
        let html = '';

        if (selectedPt) {
            html += `<span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="bi bi-building me-1"></i> ${selectedPt.nama}</span>`;
        }
        selectedDosen.forEach((d, i) => {
            html += `<span class="badge bg-white text-dark px-3 py-2 rounded-pill">Pilihan ${i + 1}: ${d.nama}</span>`;
        });
        container.innerHTML = html;

        const params = new URLSearchParams();
        if (selectedPt) params.set('pt', selectedPt.nama);
        if (selectedDosen[0]) params.set('d1', selectedDosen[0].nip);
        if (selectedDosen[1]) params.set('d2', selectedDosen[1].nip);
        if (selectedDosen[2]) params.set('d3', selectedDosen[2].nip);

        linkBtn.href = "{{ route('mahasiswa.pengajuan') }}?" + params.toString();
    }
</script>
@endpush