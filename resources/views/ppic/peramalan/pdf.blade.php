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

    <h2>Laporan Hasil Peramalan</h2>

    <div class="info">
        <strong>Kategori:</strong> {{ ucfirst($item->kategori) }}<br>
        <strong>Jenis Barang:</strong> {{ ucfirst($item->jenis_barang) }}<br>
        <strong>Tahun:</strong> {{ implode(', ', json_decode($item->tahun)) }}<br>
        <strong>Tanggal Simpan:</strong> {{ $item->created_at->format('d M Y H:i') }}
    </div>

    {{-- 1. Himpunan Semesta --}}
    <h4>1. Himpunan Semesta</h4>
    <table>
        <tr>
            <th>Min</th>
            <th>Max</th>
            <th>Min D</th>
            <th>Max D</th>
            <th>Jumlah Interval</th>
            <th>Panjang Interval</th>
        </tr>
        <tr>
            <td>{{ $data['semesta']['min'] }}</td>
            <td>{{ $data['semesta']['max'] }}</td>
            <td>{{ $data['semesta']['min_d'] }}</td>
            <td>{{ $data['semesta']['max_d'] }}</td>
            <td>{{ $data['semesta']['jumlah_interval'] }}</td>
            <td>{{ $data['semesta']['panjang_interval'] }}</td>
        </tr>
    </table>

    {{-- 2. Fuzzy Sets --}}
    <h4>2. Fuzzy Sets</h4>
    <table>
        <thead>
            <tr>
                <th>Label</th>
                <th>Range</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['fuzzySets'] as $set)
                <tr>
                    <td>{{ $set['label'] }}</td>
                    <td>{{ $set['range'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 3. Fuzzifikasi --}}
    <h4>3. Fuzzifikasi</h4>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Nilai</th>
                <th>Fuzzy</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['fuzzifikasi'] as $row)
                <tr>
                    <td>{{ $row['bulan'] }}</td>
                    <td>{{ $row['tahun'] }}</td>
                    <td>{{ $row['nilai'] }}</td>
                    <td>{{ $row['fuzzy'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 4. FLR --}}
    <h4>4. Fuzzy Logical Relationship (FLR)</h4>
    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Relasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['flr'] as $row)
                <tr>
                    <td>{{ $row['periode'] }}</td>
                    <td>{{ $row['relasi'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 5. FLRG --}}
    <h4>5. Fuzzy Logical Relationship Group (FLRG)</h4>
    <table>
        <thead>
            <tr>
                <th>Dari</th>
                <th>Ke</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['flrg'] as $from => $toList)
                <tr>
                    <td>{{ $from }}</td>
                    <td>{{ implode(', ', $toList) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 6. Defuzzifikasi --}}
    <h4>6. Hasil Defuzzifikasi</h4>
    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Aktual</th>
                <th>Hasil Peramalan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['defuzzifikasi'] as $row)
                <tr>
                    <td>{{ $row['periode'] }}</td>
                    <td>{{ $row['aktual'] }}</td>
                    <td>{{ $row['hasil'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
