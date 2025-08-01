<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Peramalan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2, h4 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 5px;
            text-align: center;
        }

        .info {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    {{-- Kop Surat --}}
    <table style="width:100%; border: none; margin-bottom: 20px;">
        <tr>
            <td style="width: 15%;">
              <img src="{{ public_path('logo.arida.png') }}" alt="Logo" width="100">
            </td>
            <td style="text-align: left; font-size: 13px;">
                <strong style="font-size: 16px;">PT. ARTERIA DAYA MULIA</strong><br>
                Jl. Dukuh Duwur No. 46, Cirebon 45113, Jawa Barat - Indonesia<br>
                Telp. (0231) 206507 | Fax. (0231) 206478 - 206842
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid black; margin-bottom: 20px;">

    {{-- Judul --}}
    <h2 style="text-align: center; margin-bottom: 5px;">Laporan Hasil Peramalan</h2>

{{-- Grafik --}}
@if(isset($chartImage))
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ $chartImage }}" alt="Grafik Peramalan" style="width: 100%;">
    </div>
@endif

    {{-- Ringkasan --}}
    <div class="info" style="margin-bottom: 10px;">
        <strong>Ringkasan Data</strong><br>
        Kategori: {{ ucfirst($item->kategori) }}<br>
        Jenis Barang: {{ ucfirst($item->jenis_barang) }}<br>
        Tahun Data: {{ implode(', ', json_decode($item->tahun)) }}
    </div>

    {{-- Kesimpulan --}}
    @php
        $awal = $data['fuzzifikasi'][0];
        $akhir = $data['defuzzifikasi'][count($data['defuzzifikasi']) - 2];
        $prediksi = end($data['defuzzifikasi'])['hasil'];
        $aktual = array_filter(array_column($data['defuzzifikasi'], 'aktual'));
        $min = !empty($aktual) ? min($aktual) : 0;
        $max = !empty($aktual) ? max($aktual) : 0;
        $jumlahData = count($aktual);
    @endphp

    <div style="margin-bottom: 20px;">
        <strong>Kesimpulan Peramalan</strong><br>
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
    </div>

    {{-- 1. Himpunan Semesta --}}
    <div class="card shadow-sm p-4 mb-4">
        <p class="text-center mb-0" style="font-size: 20px; font-weight: bold;">
    Perhitungan <em>Fuzzy Time Series Chen</em>
</p>
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
        <td>{{ number_format($semesta['min'], 0, ',', '.') }}</td>
        <td>{{ number_format($semesta['max'], 0, ',', '.') }}</td>
        <td>{{ number_format($semesta['min_d'], 0, ',', '.') }}</td>
        <td>{{ number_format($semesta['max_d'], 0, ',', '.') }}</td>
        <td>{{ $semesta['jumlah_interval'] }}</td>
        <td>{{ number_format($semesta['panjang_interval'], 0, ',', '.') }}</td>
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
            <td>{{ str_replace('→', '>', $r['relasi']) }}</td>
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
    <tr>
        <th>Periode</th>
        <th>Aktual</th>
        <th>Hasil Peramalan</th>
    </tr>
    @foreach($defuzzifikasi as $d)
        <tr>
            <td>{{ $d['periode'] }}</td>
            <td>
                @if(!is_null($d['aktual']))
                    {{ number_format($d['aktual'], 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
            <td>
                @if(!is_null($d['hasil']))
                    {{ number_format($d['hasil'], 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
</table>

@php
    use Carbon\Carbon;
    $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
@endphp

<br><br><br>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 50px;">
    <tr style="border: none;">
        <td style="width: 60%; border: none;"></td>
        <td style="text-align: center; border: none;">
            Cirebon, {{ $tanggalCetak }}<br>
            <strong>PT ARIDA</strong><br><br><br><br><br>
            <u><strong>{{ $namaManager ?? 'Nama Manajer' }}</strong></u><br>
            Manajer Produksi
        </td>
    </tr>
</table>

</body>
</html>
