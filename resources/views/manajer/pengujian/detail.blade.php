@extends('layouts.manajer')

@section('title', $title)

@section('content')
@php
    $kategoriMap = [
        'bahanbaku' => 'Bahan Baku',
        'produksi' => 'Produksi'
    ];
@endphp
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow-sm p-4 mb-4">
        <p><strong>Kategori:</strong> {{ $kategoriMap[$pengujian->kategori] ?? ucfirst($pengujian->kategori) }}</p>
        <p><strong>Jenis Barang:</strong> {{ ucfirst($pengujian->jenis_barang) }}</p>
        <p><strong>Tahun:</strong> {{ implode(', ', json_decode($pengujian->tahun, true)) }}</p>
        <hr>
        <p><strong>MSE:</strong> {{ number_format($mse, 2, '.', '.') }}</p>
        <p><strong>MAPE:</strong> {{ number_format($mape, 2, '.', '.') }}%</p>
    </div>

    <h5 class="mt-4">Tabel Perbandingan Aktual vs Hasil</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center align-middle">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 10%">Bulan</th>
                    <th style="width: 15%">Aktual (ribu)</th>
                    <th style="width: 15%">Hasil (ribu)</th>
                    <th style="width: 15%">Selisih</th>
                    <th style="width: 20%">MSE</th>
                    <th style="width: 15%">MAPE (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil as $d)
                    @if (isset($d['hasil']) && $d['hasil'] !== null && $d['aktual'] != 0)
                        @php
                            $aktual = $d['aktual'] / 1000;
                            $hasilForecast = $d['hasil'] / 1000;
                            $error = $aktual - $hasilForecast;
                            $mseValue = pow($error, 2);
                            $mapeValue = abs($error / $aktual) * 100;
                        @endphp
                        <tr>
                            <td>{{ $d['periode'] ?? '-' }}</td>
                            <td>{{ number_format($aktual, 3, '.', '.') }}</td>
                            <td>{{ number_format($hasilForecast, 3, '.', '.') }}</td>
                            <td>{{ number_format($error, 3, '.', '.') }}</td>
                            <td>{{ number_format($mseValue, 2, '.', '.') }}</td>
                            <td>{{ number_format($mapeValue, 2, '.', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
