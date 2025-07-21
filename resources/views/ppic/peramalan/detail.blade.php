@extends('layouts.ppic')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="mb-3">
        <strong>Kategori:</strong> {{ ucfirst($item->kategori) }}<br>
        <strong>Jenis Barang:</strong> {{ ucfirst($item->jenis_barang) }}<br>
        <strong>Tahun:</strong> {{ implode(', ', json_decode($item->tahun)) }}
    </div>

    {{-- 1. Himpunan Semesta --}}
    <h5 class="mt-4">1. Himpunan Semesta</h5>
    <table class="table table-bordered table-sm">
        <tr>
            <th>Data Min</th>
            <th>Data Max</th>
            <th>Min - D</th>
            <th>Max + D</th>
            <th>Jumlah Interval</th>
            <th>Panjang Interval</th>
        </tr>
        <tr>
            <td>{{ $semesta['min'] }}</td>
            <td>{{ $semesta['max'] }}</td>
            <td>{{ $semesta['min_d'] }}</td>
            <td>{{ $semesta['max_d'] }}</td>
            <td>{{ $semesta['jumlah_interval'] }}</td>
            <td>{{ $semesta['panjang_interval'] }}</td>
        </tr>
    </table>

    {{-- 2. Fuzzy Set --}}
    <h5 class="mt-4">2. Fuzzy Set</h5>
<table class="table table-bordered table-sm">
    <thead class="table-secondary">
        <tr>
            <th>Interval</th>
            <th>Fuzzy Set</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fuzzySets as $fs)
        <tr>
            <td>
                @php
                    [$awal, $akhir] = explode(' - ', $fs['range']);
                    $awal = number_format($awal, 0, ',', '.');
                    $akhir = number_format($akhir, 0, ',', '.');
                @endphp
                {{ $awal }} - {{ $akhir }}
            </td>
            <td>{{ $fs['label'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


    {{-- 3. Fuzzifikasi --}}
    <h5 class="mt-4">3. Fuzzifikasi</h5>
    <table class="table table-bordered table-sm">
        <tr><th>Bulan</th><th>Tahun</th><th>Data</th><th>Fuzzy</th></tr>
        @foreach($fuzzifikasi as $row)
            <tr>
                <td>{{ $row['bulan'] }}</td>
                <td>{{ $row['tahun'] }}</td>
                <td>{{ number_format($row['nilai'], 0, ',', '.') }}</td>
                <td>{{ $row['fuzzy'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- 4. FLR --}}
    <h5 class="mt-4">4. FLR</h5>
    <table class="table table-bordered table-sm">
        <tr><th>Periode</th><th>Relasi</th></tr>
        @foreach($flr as $r)
            <tr>
                <td>{{ $r['periode'] }}</td>
                <td>{{ $r['relasi'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- 5. FLRG --}}
    <h5 class="mt-4">5. FLRG</h5>
    <table class="table table-bordered table-sm">
        <tr><th>Fuzzy</th><th>Relasi</th></tr>
        @foreach($flrg as $k => $v)
            <tr>
                <td>{{ $k }}</td>
                <td>{{ implode(', ', $v) }}</td>
            </tr>
        @endforeach
    </table>

    {{-- 6. Defuzzifikasi --}}
    <h5 class="mt-4">6. Defuzzifikasi</h5>
    <table class="table table-bordered table-sm">
        <tr><th>Periode</th><th>Aktual</th><th>Hasil Peramalan</th></tr>
        @foreach($defuzzifikasi as $d)
            <tr>
                <td>{{ $d['periode'] }}</td>
                <td>{{ number_format($d['aktual'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['hasil'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
