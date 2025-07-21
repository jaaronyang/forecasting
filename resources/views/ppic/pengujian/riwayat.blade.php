@extends('layouts.ppic')

@section('title', $title)

@section('content')
@php
    $kategoriMap = ['bahanbaku' => 'Bahan Baku', 'produksi' => 'Produksi'];
@endphp

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Riwayat Hasil Pengujian</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Jenis Barang</th>
                        <th>Tahun</th>
                        <th>Waktu Simpan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</td>
                            <td>{{ ucfirst($item->jenis_barang) }}</td>
                            <td>
                                @php
                                    $tahun = json_decode($item->tahun, true);
                                    echo is_array($tahun) ? implode(', ', $tahun) : $item->tahun;
                                @endphp
                            </td>
                            <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <a href="{{ route('ppic.pengujian.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                <a href="{{ route('ppic.pengujian.download', $item->id) }}" target="_blank" class="btn btn-success btn-sm">PDF</a>
                                <a href="#" class="btn btn-danger btn-sm"
                                   onclick="event.preventDefault(); if(confirm('Yakin hapus data ini?')) document.getElementById('delete-form-{{ $item->id }}').submit();">
                                    Hapus
                                </a>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('ppic.pengujian.destroy', $item->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
                                {{-- <a href="{{ route('pengujian.pdf', $item->id) }}" class="btn btn-secondary btn-sm">PDF</a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Belum ada data pengujian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
