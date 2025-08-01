<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengujian Peramalan</title>
    <style>
        /* ... style sesuai yang kamu punya ... */
        body { font-family: sans-serif; font-size: 11px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 4px; text-align: center; }
        .info, .kesimpulan { margin-bottom: 20px; }
        .logo { width: 100px; }
        .kop { border: none; margin-bottom: 10px; }
        ul { margin: 0; padding-left: 20px; }
        .text-justify { text-align: justify; }
    </style>
</head>

<body>

{{-- Kop Surat --}}
<table class="kop">
    <tr>
        <td style="width: 15%;">
            <img src="{{ public_path('logo.arida.png') }}" class="logo" alt="Logo">
        </td>
        <td style="text-align: left; font-size: 13px;">
            <strong style="font-size: 15px;">PT. ARTERIA DAYA MULIA</strong><br>
            Jl. Dukuh Duwur No. 46, Cirebon 45113, Jawa Barat - Indonesia<br>
            Telp. (0231) 206507 | Fax. (0231) 206478 - 206842
        </td>
    </tr>
</table>

<hr style="border: 1px solid black; margin-bottom: 20px;">

<h2>Laporan Pengujian Peramalan</h2>

{{-- Ringkasan --}}
<div class="info">
    <strong>Ringkasan Data</strong><br>
    Kategori: {{ ucfirst($item->kategori) }}<br>
    Jenis Barang: {{ ucfirst($item->jenis_barang) }}<br>
    Tahun Data: {{ implode(', ', json_decode($item->tahun)) }}
</div>

{{-- Kesimpulan --}}
@php
    $mape = $item->mape;
    $mse = $item->mse;

    $tingkatAkurasi = match (true) {
        $mape <= 10 => 'Sangat Baik',
        $mape <= 20 => 'Baik',
        $mape <= 50 => 'Cukup',
        default => 'Kurang',
    };
@endphp

<h5 class="font-weight-bold text-success mb-3">Kesimpulan Pengujian</h5>
<p class="text-justify">
    Hasil pengujian akurasi peramalan untuk kategori
    <strong>{{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</strong>
    dengan jenis barang <strong>{{ ucfirst($item->jenis_barang) }}</strong> pada periode
    <strong>{{ $periodeAwal }}</strong> hingga <strong>{{ $periodeAkhir }}</strong> menunjukkan bahwa nilai
    <strong>Mean Squared Error (MSE)</strong> yang diperoleh adalah
    <strong>{{ number_format($mse, 0, '.', '.') }}</strong> dan nilai
    <strong>Mean Absolute Percentage Error (MAPE)</strong> adalah
    <strong>{{ number_format($mape, 0, '.', '.') }}%</strong>. Berdasarkan nilai tersebut, tingkat akurasi peramalan dikategorikan
    <strong>
        @if ($mape <= 10)
            Sangat Baik
        @elseif ($mape <= 20)
            Baik
        @elseif ($mape <= 50)
            Cukup
        @else
            Kurang
        @endif
    </strong>.
    Selain itu, diperoleh hasil nilai peramalan pada periode selanjutnya yaitu
    <strong>{{ $bulanBerikutnya }} {{ $tahunBerikutnya }}</strong> sebesar
    <strong>{{ number_format($hasilPeramalan, 0, '.', '.') }}</strong>. Nilai ini menunjukkan bahwa metode yang digunakan dalam sistem
    @if ($mape <= 20)
        layak dijadikan acuan dalam pengambilan keputusan produksi dan pengadaan bahan baku.
    @else
        masih perlu dilakukan evaluasi lebih lanjut untuk meningkatkan akurasi pada masa mendatang.
    @endif
</p>

{{-- Tabel Perbandingan --}}
<p style="font-weight: bold; text-align: center; font-size: 14px;">Tabel Perbandingan Aktual vs Hasil</p>
<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Aktual<br>(ribu)</th>
            <th>Hasil<br>(ribu)</th>
            <th>Selisih</th>
            <th>MSE</th>
            <th>MAPE (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
    @php
        $aktual = $d['aktual'] ?? null;
        $hasil = $d['hasil'] ?? null;
        $periode = $d['periode'] ?? ($d['bulan'] ?? '-');
        $tahun = $d['tahun'] ?? '-';
        $periodeFull = $periode . ' ' . $tahun;

        $aktualRibu = $aktual !== null ? $aktual / 1000 : null;
        $hasilRibu = $hasil / 1000;
        $error = $aktualRibu !== null ? $aktualRibu - $hasilRibu : null;
        $mseValue = $error !== null ? pow($error, 2) : null;
        $mapeValue = ($error !== null && $aktualRibu != 0) ? abs($error / $aktualRibu) * 100 : null;
    @endphp

    @if ($hasil !== null)
        <tr>
            <td>{{ $periodeFull }}</td>
            <td>{{ $aktualRibu !== null ? number_format($aktualRibu, 3, '.', '.') : '-' }}</td>
            <td>{{ number_format($hasilRibu, 3, '.', '.') }}</td>
            <td>{{ $error !== null ? number_format($error, 3, '.', '.') : '-' }}</td>
            <td>{{ $mseValue !== null ? number_format($mseValue, 2, '.', '.') : '-' }}</td>
            <td>{{ $mapeValue !== null ? number_format($mapeValue, 2, '.', '.') : '-' }}</td>
        </tr>
    @endif
@endforeach
    </tbody>
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
