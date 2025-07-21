@extends('layouts.ppic')

@section('title', 'Edit Bahan Baku Jaring')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Data Bahan Baku Jaring</h1>

    <form action="{{ route('bahanbaku.jaring.update', $data->id) }}" method="POST">
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
            <label for="jumlah_bahanbaku">Jumlah Bahan Baku</label>
            <input type="text" name="jumlah_bahanbaku" id="jumlah_bahanbaku" class="form-control" value="{{ $data->jumlah_bahanbaku }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('bahanbaku.jaring.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
<script>
    new AutoNumeric('#jumlah_bahanbaku', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        unformatOnSubmit: true
    });
</script>
@endsection
