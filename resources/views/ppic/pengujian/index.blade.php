@extends('layouts.ppic')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengujian Akurasi Peramalan</h1>

    <form method="POST" action="{{ route('ppic.pengujian.hitung') }}">
        @csrf
        <div class="form-group">
            <label for="peramalan_id">Pilih Data Peramalan</label>
            <select name="peramalan_id" id="peramalan_id" class="form-control" required>
                <option value="">-- Pilih --</option>
                @foreach($dataPeramalan as $item)
                    <option value="{{ $item->id }}">
                        {{ ucfirst($item->kategori) }} - {{ ucfirst($item->jenis_barang) }} ({{ implode(', ', json_decode($item->tahun)) }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Hitung Pengujian</button>
    </form>
</div>
@endsection
