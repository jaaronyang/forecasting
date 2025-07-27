@extends('layouts.ppic')

@section('title', 'Hasil Peramalan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Peramalan Fuzzy Time Series Lee</h1>

    {{-- Info Umum --}}
    @php
$tahunList = is_array($item->tahun) ? $item->tahun : json_decode($item->tahun, true);
@endphp

<div class="card shadow-sm p-4 mb-4">
    <h5 class="font-weight-bold text-primary mb-3">Ringkasan Data</h5>
    <p class="mb-2"><strong>Kategori:</strong> {{ ucfirst($item->kategori) }}</p>
    <p class="mb-2"><strong>Jenis Barang:</strong> {{ ucfirst($item->jenis_barang) }}</p>
    <p class="mb-2"><strong>Tahun Data:</strong> {{ implode(', ', $tahunList) }}</p><br>
@if(isset($hasilPeramalanTerakhir) && !is_null($hasilPeramalanTerakhir['hasil']))
    <h5 class="font-weight-bold text-success mb-3">Kesimpulan Peramalan</h5>
    <p class="text-justify">
        Berdasarkan proses peramalan menggunakan metode <em>Fuzzy Time Series Chen</em> untuk kategori
        <strong>{{ strtolower($item->kategori) }}</strong> barang
        <strong>{{ strtolower($item->jenis_barang) }}</strong>, pada periode
       <strong>{{ $bulanAwal }} {{ $tahunAwal }}</strong> hingga <strong>{{ $bulanAkhir }} {{ $tahunAkhir }}</strong>,
        menggunakan data sebanyak <strong>{{ $jumlahData }}</strong> bulan,
        dengan nilai terendah sebesar <strong>{{ number_format($nilaiTerendah, 0, ',', '.') }} kg</strong> dan
        tertinggi sebesar <strong>{{ number_format($nilaiTertinggi, 0, ',', '.') }} kg</strong>.
        Diperkirakan jumlah <strong>{{ strtolower($item->kategori) }}</strong> pada bulan
        <strong>{{ $hasilPeramalanTerakhir['periode'] }} {{ $hasilPeramalanTerakhir['tahun'] }}</strong>
        adalah sebesar <strong>{{ number_format($hasilPeramalanTerakhir['hasil'], 0, ',', '.') }} kg</strong>.
    </p>
@endif


    {{-- 1. Himpunan Semesta --}}
    <div class="card shadow-sm p-4 mb-4">
        <p class="text-center mb-0" style="font-size: 20px; font-weight: bold;">
    Perhitungan <em>Fuzzy Time Series Chen</em>
</p>
    <h5 class="mt-4">1. Himpunan Semesta</h5>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>Data Min</th>
                <th>Data Max</th>
                <th>Min - D</th>
                <th>Max + D</th>
                <th>Jumlah Interval</th>
                <th>Panjang Interval</th>
            </tr>
        </thead>
        <tbody>
            <tr>
    <td>{{ number_format($semesta['min'], 0, ',', '.') }}</td>
    <td>{{ number_format($semesta['max'], 0, ',', '.') }}</td>
    <td>{{ number_format($semesta['min_d'], 0, ',', '.') }}</td>
    <td>{{ number_format($semesta['max_d'], 0, ',', '.') }}</td>
    <td>{{ $semesta['jumlah_interval'] }}</td>
    <td>{{ number_format($semesta['panjang_interval'], 0, ',', '.') }}</td>
</tr>
        </tbody>
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
    <h5 class="mt-4">3. Fuzzifikasi Data Historis</h5>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Data</th>
                <th>Fuzzy</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fuzzifikasi as $row)
            <tr>
                <td>{{ $row['bulan'] }}</td>
                <td>{{ $row['tahun'] }}</td>
                <td>{{ number_format($row['nilai'], 0, ',', '.') }}</td>
                <td>{{ $row['fuzzy'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. FLR --}}
    <h5 class="mt-4">4. Fuzzy Logical Relationship (FLR)</h5>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>Periode</th>
                <th>FLR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flr as $item)
            <tr>
                <td>{{ $item['periode'] }}</td>
                <td>{{ $item['relasi'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 5. FLRG --}}
    <h5 class="mt-4">5. Fuzzy Logical Relationship Group (FLRG)</h5>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>Fuzzy</th>
                <th>Relasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flrg as $key => $relasi)
            <tr>
                <td>{{ $key }}</td>
                <td>{{ implode(', ', $relasi) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 6. Defuzzifikasi --}}
    <h5 class="mt-4">6. Defuzzifikasi</h5>
    <table class="table table-bordered table-sm">
        <thead class="table-secondary">
            <tr>
                <th>Periode</th>
                <th>Data Asli</th>
                <th>Hasil Peramalan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($defuzzifikasi as $d)
            <tr>
                <td>{{ $d['periode'] }}</td>
                <td>{{ number_format($d['aktual'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['hasil'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
