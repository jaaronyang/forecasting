@extends('layouts.manajer')

@section('title', 'Data Hasil Pengujian')

@section('content')
@php
    $kategoriMap = ['bahanbaku' => 'Bahan Baku', 'produksi' => 'Produksi'];
@endphp

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Riwayat Hasil Pengujian</h1>

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
                    @foreach($data as $key => $item)
<tr>
    <td>{{ $key + 1 }}</td>
    <td>{{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</td>
    <td>{{ ucfirst($item->jenis_barang) }}</td>
    @php
        $tahun = json_decode($item->tahun);
    @endphp
    <td>
        {{ is_array($tahun) ? implode(', ', $tahun) : $item->tahun }}
    </td>
    <td>{{ $item->created_at ? $item->created_at->format('d-m-Y') : '-' }}</td>
    <td>
        <a href="{{ route('manajer.pengujian.detail', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
        <a href="{{ route('manajer.pengujian.download', $item->id) }}" class="btn btn-success btn-sm">PDF</a>
    </td>
</tr>
@endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
