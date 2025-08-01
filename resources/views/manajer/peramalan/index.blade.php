@extends('layouts.manajer')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Riwayat Data Peramalan</h1>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
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
                @php
    $tahun = json_decode($item->tahun);
@endphp
<td>
    {{ is_array($tahun) ? implode(', ', $tahun) : $item->tahun }}
</td>
                <td>{{ $item->created_at ? $item->created_at->format('d-m-Y') : '-' }}</td>
                <td>
                    <a href="{{ route('manajer.peramalan.detail', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('manajer.peramalan.download', $item->id) }}" class="btn btn-success btn-sm">PDF</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    </div>
</div>
@endsection
