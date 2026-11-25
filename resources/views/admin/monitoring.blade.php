@extends('layouts.admin')
@section('title', 'Monitoring Dokumen & Nilai')
@section('content')
<div class="bg-white border rounded-4 p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monitoring Nilai & Kelulusan PKL</h4>
            <p class="text-muted small mb-0">Rekapitulasi nilai pembimbing industri (F5), kampus (F6), dan status kelulusan mahasiswa.</p>
        </div>
        <button onclick="window.print()" class="btn btn-sm text-white fw-semibold px-3 py-2 rounded-3" style="background: var(--navy);">
            <i class="bi bi-printer me-1"></i> Cetak Rekap Prodi
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle small">
            <thead class="table-light text-center">
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Instansi</th>
                    <th>Industri (60%)</th>
                    <th>Kampus (40%)</th>
                    <th>Nilai Akhir</th>
                    <th>Grade</th>
                    <th>Status Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataMonitoring as $m)
                <tr>
                    <td class="text-center">{{ $m->nim }}</td>
                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                    <td>{{ $m->nama_perusahaan ?? '-' }}</td>
                    <td class="text-center">{{ $m->rata_rata_industri ?? '-' }}</td>
                    <td class="text-center">{{ $m->rata_rata_kampus ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $m->nilai_akhir_angka ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $m->nilai_akhir_huruf ?? '-' }}</td>
                    <td class="text-center text-capitalize">{{ str_replace('_', ' ', $m->status_kelulusan ?? 'belum_lengkap') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection