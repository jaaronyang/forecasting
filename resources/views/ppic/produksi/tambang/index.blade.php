@extends('layouts.ppic')

@section('title', 'Produksi Tambang')

@section('content')

@if(session('success'))
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
@endif

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 text-gray-800">Data Produksi Tambang</h1>
    <a href="{{ route('produksi.tambang.create') }}" class="btn btn-primary">+ Tambah Data</a>
</div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Jumlah Produksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                   <tbody>
    @foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->bulan }}</td>
        <td>{{ $item->tahun }}</td>
        <td>{{ number_format($item->jumlah_produksi, 0, ',', '.') }}</td>
<td>
    <a href="{{ route('produksi.tambang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>

    <form action="{{ route('produksi.tambang.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
    </form>
</td>
    </tr>
    @endforeach
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection
