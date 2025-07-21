@extends('layouts.ppic')

@section('title', 'Edit Data Jaring')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
<script>
    new AutoNumeric('#jumlah_produksi', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        unformatOnSubmit: true
    });
</script>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Data Produksi Jaring</h1>

    <form action="{{ route('produksi.jaring.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="bulan">Bulan</label>
            <select name="bulan" class="form-control" required>
                @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                    <option value="{{ $bulan }}" {{ $data->bulan == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tahun">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="{{ $data->tahun }}" required>
        </div>
        <div class="form-group">
            <label for="jumlah_produksi">Jumlah Produksi</label>
            <input type="text" name="jumlah_produksi" id="jumlah_produksi" class="form-control" value="{{ $data->jumlah_produksi }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('produksi.jaring.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
