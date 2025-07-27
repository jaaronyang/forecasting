@extends('layouts.ppic')

@section('title', $title)

@section('content')
@php
    $kategoriMap = [
        'bahanbaku' => 'Bahan Baku',
        'produksi' => 'Produksi'
    ];
@endphp

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Pengujian Peramalan</h1>

    <!-- Ringkasan Hasil -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="font-weight-bold text-primary mb-3">Ringkasan Data</h5>
            <p><strong>Kategori:</strong> {{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</p>
        <p><strong>Jenis Barang:</strong> {{ ucfirst($item->jenis_barang) }}</p>
        <p><strong>Tahun:</strong> {{ implode(', ', json_decode($item->tahun, true)) }}</p>
    <h5 class="font-weight-bold text-success mb-3">Kesimpulan Pengujian</h5>
<p class="text-justify">
    Hasil pengujian akurasi peramalan untuk kategori
    <strong>{{ $kategoriMap[$item->kategori] ?? ucfirst($item->kategori) }}</strong>
    dengan jenis barang <strong>{{ ucfirst($item->jenis_barang) }}</strong> pada periode
    <strong>{{ $bulanAwal }} {{ $tahunAwal }}</strong> hingga <strong>{{ $bulanAkhir }} {{ $tahunAkhir }}</strong> menunjukkan bahwa nilai
    <strong>Mean Squared Error (MSE)</strong> yang diperoleh adalah
    <strong>{{ number_format($mse, 3, '.', '.') }}</strong> dan nilai
    <strong>Mean Absolute Percentage Error (MAPE)</strong> adalah
    <strong>{{ number_format($mape, 3, '.', '.') }}%</strong>. Berdasarkan nilai tersebut, tingkat akurasi peramalan dikategorikan
    <strong>
        @if ($item->mape <= 10)
            Sangat Baik
        @elseif ($item->mape <= 20)
            Baik
        @elseif ($item->mape <= 50)
            Cukup
        @else
            Kurang
        @endif
    </strong>.
    Selain itu, diperoleh hasil nilai peramalan pada periode selanjutnya yaitu
    <strong>{{ $bulanBerikutnya }} {{ $tahunBerikutnya }}</strong> sebesar
    <strong>{{ number_format($hasilPeramalan, 0, '.', '.') }}</strong>. Nilai ini menunjukkan bahwa metode yang digunakan dalam sistem
    @if ($item->mape <= 20)
        layak dijadikan acuan dalam pengambilan keputusan produksi dan pengadaan bahan baku.
    @else
        masih perlu dilakukan evaluasi lebih lanjut untuk meningkatkan akurasi pada masa mendatang.
    @endif
</p>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mean Squared Error (MSE)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($mse, 3, '.', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Mean Absolute Percentage Error (MAPE)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($mape, 3, '.', '.') }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Perbandingan -->
    <h5 class="mt-4 mb-3 text-gray-800">Tabel Perbandingan Aktual vs Hasil</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center align-middle">
            <thead class="thead-light">
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
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
            $bulan = $d['bulan'] ?? ($d['periode'] ?? '-');
            $tahun = $d['tahun'] ?? '-';
        @endphp

        @if ($hasil !== null)
            @php
                $aktualRibu = $aktual !== null ? $aktual / 1000 : null;
                $hasilRibu = $hasil / 1000;
                $error = ($aktualRibu !== null) ? $aktualRibu - $hasilRibu : null;
                $mseValue = ($error !== null) ? pow($error, 2) : null;
                $mapeValue = ($error !== null && $aktualRibu != 0) ? abs($error / $aktualRibu) * 100 : null;
            @endphp
            <tr>
                <td>{{ $bulan }}</td>
                <td>{{ $tahun }}</td>
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
    </div>

    <a href="{{ route('ppic.pengujian.index') }}" class="btn btn-secondary mt-4">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
@endsection
