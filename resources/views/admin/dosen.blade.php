@extends('layouts.admin')
@section('title', 'Kelola Kuota Dosen Pembimbing')
@section('content')
@if(session('success'))
    <div class="alert border-0 mb-3" style="background: #E5F4EA; color: #278052; border-radius: 12px;">{{ session('success') }}</div>
@endif
<div class="bg-white border rounded-4 p-4 shadow-sm">
    <h4 class="fw-bold mb-1">Kelola Kuota Dosen Pembimbing</h4>
    <p class="text-muted small mb-4">Atur batas kuota mahasiswa bimbingan PKL per dosen prodi.</p>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small">
                <tr>
                    <th>Nama Dosen</th>
                    <th>NIP</th>
                    <th>Prodi Homebase</th>
                    <th>Terisi / Maksimal</th>
                    <th width="200">Ubah Kuota Maksimal</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($dosenList as $d)
                <tr>
                    <td class="fw-bold">{{ $d->nama_lengkap }}</td>
                    <td>{{ $d->nip }}</td>
                    <td>{{ $d->prodi_homebase }}</td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            {{ $d->kuota_terpakai }} / {{ $d->kuota_maksimal }} Mahasiswa
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.dosen.kuota', $d->nip) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="number" name="kuota_maksimal" value="{{ $d->kuota_maksimal }}" min="1" max="30" class="form-control form-control-sm rounded-3">
                            <button type="submit" class="btn btn-sm text-white fw-semibold" style="background: var(--navy);">Simpan</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection