@extends('layouts.ppic')

@section('title', 'Peramalan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Peramalan Data Produksi / Bahan Baku</h1>

    <form action="{{ route('peramalan.proses') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="kategori">Pilih Kategori Data</label>
            <select name="kategori" id="kategori" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="produksi">Produksi</option>
                <option value="bahanbaku">Bahan Baku</option>
            </select>
        </div>

        <div class="form-group">
            <label for="jenis_barang">Jenis Barang</label>
            <select name="jenis_barang" id="jenis_barang" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="tambang">Tambang</option>
                <option value="jaring">Jaring</option>
                <option value="benang">Benang</option>
            </select>
        </div>

        <div class="form-group">
    <label for="tahun">Pilih Tahun (Bisa lebih dari satu)</label>
    <div class="card shadow-sm border-left-primary p-3">
        <div class="d-flex flex-wrap">
            @foreach ($daftarTahun as $tahun)
                <div class="form-check mr-4 mb-2">
                    <input class="form-check-input" type="checkbox" name="tahun[]" value="{{ $tahun }}" id="tahun{{ $tahun }}">
                    <label class="form-check-label" for="tahun{{ $tahun }}">
                        {{ $tahun }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    <small class="form-text text-muted mt-2">
        Centang satu atau beberapa tahun yang ingin kamu ramalkan.
    </small>
</div>


        <button type="submit" class="btn btn-primary">Proses Peramalan</button>
    </form>
</div>
@endsection
