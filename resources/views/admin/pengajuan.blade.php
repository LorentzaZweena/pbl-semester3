@extends('layouts.admin')

@section('title', 'Verifikasi Pengajuan PKL')

@section('content')
@if(session('success'))
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2" style="background: #E5F4EA; color: #278052; border-radius: 12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<div class="bg-white border rounded-4 p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Verifikasi Pengajuan PKL Mahasiswa</h4>
            <p class="text-muted small mb-0">Periksa kelengkapan berkas, berikan catatan revisi, atau sahkan pengajuan PKL.</p>
        </div>

        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="disetujui_admin" {{ request('status') == 'disetujui_admin' ? 'selected' : '' }}>Disetujui Admin</option>
                <option value="aktif_pkl" {{ request('status') == 'aktif_pkl' ? 'selected' : '' }}>Aktif PKL</option>
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light small">
                <tr>
                    <th>Mahasiswa</th>
                    <th>Instansi / Perusahaan</th>
                    <th>Usulan Dosbing</th>
                    <th>Surat Pengantar / Penerimaan</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($pengajuanList as $p)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $p->nama_mahasiswa }}</div>
                            <div class="text-muted">{{ $p->nim }} • {{ $p->prodi }} ({{ $p->kelas }})</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $p->nama_perusahaan ?? '-' }}</div>
                            <div class="text-muted" style="font-size: 0.78rem;">
                                {{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y') }} –
                                {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <div>{{ $p->nama_dosbing_1 ?? 'Belum memilih' }}</div>
                            @if($p->nama_dosbing_terpilih)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Tetap: {{ $p->nama_dosbing_terpilih }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($p->file_bukti_keterimaan)
                                <a href="{{ asset('storage/' . $p->file_bukti_keterimaan) }}" target="_blank" class="btn btn-sm btn-light border rounded-3">
                                    <i class="bi bi-file-pdf text-danger me-1"></i> Lihat Berkas
                                </a>
                            @else
                                <span class="text-muted fst-italic">Belum diunggah</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeStyle = match($p->status_verifikasi) {
                                    'disetujui_admin' => 'background:#E7F0FC; color:#346BA8;',
                                    'aktif_pkl'       => 'background:#E5F4EA; color:#278052;',
                                    'revisi'          => 'background:#FCE9E8; color:#B94141;',
                                    'ditolak'         => 'background:#FCE9E8; color:#B94141;',
                                    default           => 'background:#FFF2D7; color:#A86D12;',
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 text-capitalize" style="{{ $badgeStyle }}">
                                {{ str_replace('_', ' ', $p->status_verifikasi) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-sm text-white fw-semibold px-3 rounded-3"
                                    style="background: var(--navy);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalVerifikasi{{ $p->id }}">
                                Verifikasi
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Verifikasi per Pengajuan -->
                    <div class="modal fade" id="modalVerifikasi{{ $p->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <div class="modal-header border-bottom px-4 py-3" style="background: var(--bg-main);">
                                    <div>
                                        <h5 class="modal-title fw-bold mb-0" style="color: var(--navy);">Verifikasi Pengajuan PKL</h5>
                                        <small class="text-muted">{{ $p->nama_mahasiswa }} ({{ $p->nim }})</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('admin.pengajuan.verifikasi', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Keputusan Verifikasi<span class="text-danger">*</span></label>
                                            <select name="status_verifikasi" class="form-select rounded-3" required>
                                                <option value="disetujui_admin" {{ $p->status_verifikasi == 'disetujui_admin' ? 'selected' : '' }}>Setujui Pengajuan (Terbitkan Surat)</option>
                                                <option value="aktif_pkl" {{ $p->status_verifikasi == 'aktif_pkl' ? 'selected' : '' }}>Aktifkan Status PKL (Surat Balasan Diterima)</option>
                                                <option value="revisi" {{ $p->status_verifikasi == 'revisi' ? 'selected' : '' }}>Kembalikan untuk Revisi</option>
                                                <option value="ditolak" {{ $p->status_verifikasi == 'ditolak' ? 'selected' : '' }}>Tolak Pengajuan</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Tetapkan Dosen Pembimbing Kampus</label>
                                            <select name="dosbing_terpilih" class="form-select rounded-3">
                                                <option value="">-- Pilih Dosen Pembimbing --</option>
                                                @foreach($dosenList as $dsn)
                                                    <option value="{{ $dsn->nip }}" {{ ($p->dosbing_terpilih ?? $p->opsi_dosbing_1) == $dsn->nip ? 'selected' : '' }}>
                                                        {{ $dsn->nama_lengkap }} (Kuota: {{ $dsn->kuota_terpakai }}/{{ $dsn->kuota_maksimal }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-bold small">Catatan Admin Prodi (Wajib diisi jika Revisi/Ditolak)</label>
                                            <textarea name="catatan_admin" rows="3" class="form-control rounded-3" placeholder="Tuliskan catatan perbaikan berkas atau nomor surat...">{{ $p->catatan_admin }}</textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top px-4 py-3">
                                        <button type="button" class="btn btn-light border rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn text-white fw-semibold rounded-3 px-4" style="background: #278052;">
                                            Simpan Keputusan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada data pengajuan PKL sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection