<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Pengujian Peramalan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2, h4 {
            text-align: center;
        }

        .info {
            margin-bottom: 20px;
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
    </style>
</head>
<body>

<h2>Laporan Hasil Pengujian Peramalan</h2>

<div class="info">
    <strong>Kategori:</strong> {{ ucfirst($item->kategori) }}<br>
    <strong>Jenis Barang:</strong> {{ ucfirst($item->jenis_barang) }}<br>
    <strong>Tahun:</strong> {{ is_array(json_decode($item->tahun)) ? implode(', ', json_decode($item->tahun)) : $item->tahun }}<br>
    <strong>Tanggal Uji:</strong> {{ $item->created_at->format('d M Y') }}<br>
    <strong>MSE:</strong> {{ number_format($mse, 2, '.', '.') }}<br>
    <strong>MAPE:</strong> {{ number_format($mape, 2, '.', '.') }}%
</div>

<h4>Tabel Perbandingan Aktual vs Hasil</h4>
<table>
    <thead>
        <tr>
            <th>Periode</th>
            <th>Aktual (ribu)</th>
            <th>Hasil (ribu)</th>
            <th>Error</th>
            <th>MSE</th>
            <th>MAPE</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($data as $d)
        @php
            $aktual = isset($d['aktual']) ? $d['aktual'] / 1000 : 0;
            $hasil = isset($d['hasil']) ? $d['hasil'] / 1000 : 0;
            $error = $aktual - $hasil;
            $mseValue = pow($error, 2);
            $mapeValue = $aktual != 0 ? abs($error / $aktual) * 100 : 0;
        @endphp
        <tr>
            <td>{{ $d['periode'] ?? '-' }}</td>
            <td>{{ number_format($aktual, 3, '.', '.') }}</td>
            <td>{{ number_format($hasil, 3, '.', '.') }}</td>
            <td>{{ number_format($error, 3, '.', '.') }}</td>
            <td>{{ number_format($mseValue, 2, '.', '.') }}</td>
            <td>{{ number_format($mapeValue, 2, '.', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada data pengujian ditemukan.</td>
        </tr>
    @endforelse
</tbody>

</table>

</body>
</html>
