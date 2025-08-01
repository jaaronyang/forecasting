@extends('layouts.ppic')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Riwayat Data Peramalan</h1>
@if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-sm">
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
    @php
    $kategoriMap = [
        'bahanbaku' => 'Bahan Baku',
        'produksi' => 'Produksi'
    ];
@endphp

    @foreach($dataPeramalan as $key => $item)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</td>
        <td>{{ ucfirst($item->jenis_barang) }}</td>
        <td>{{ implode(', ', json_decode($item->tahun)) }}</td>
        <td>{{ $item->created_at->format('d-m-Y') }}</td>
        <td>
            <a href="{{ route('peramalan.detail', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
            <a href="{{ route('peramalan.download', $item->id) }}" class="btn btn-success btn-sm">PDF</a>
            <form action="{{ route('peramalan.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</tbody>

    </table>
</div>
@endsection
