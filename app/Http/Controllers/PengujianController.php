<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeramalan;
use App\Models\DataPengujian;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengujianController extends Controller
{
private function getKategoriAkurasi($mape)
{
    if ($mape <= 10) {
        return 'Sangat Baik';
    } elseif ($mape <= 20) {
        return 'Baik';
    } elseif ($mape <= 50) {
        return 'Cukup';
    }
    return 'Kurang';
}
    public function index()
    {
        $dataPeramalan = DataPeramalan::all();

        return view('ppic.pengujian.index', [
            'dataPeramalan' => $dataPeramalan,
            'title' => 'Pengujian Akurasi Peramalan'
        ]);
    }

   public function hitung(Request $request)
{
    $item = DataPeramalan::findOrFail($request->peramalan_id);
    $hasil = json_decode($item->hasil_peramalan, true);

    $data = $hasil['defuzzifikasi'] ?? [];

    $totalMSE = 0;
    $totalMAPE = 0;
    $n = 0;

    $urutanBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Urutkan data berdasarkan tahun dan bulan
    usort($data, function ($a, $b) use ($urutanBulan) {
        $tahunA = $a['tahun'] ?? 0;
        $tahunB = $b['tahun'] ?? 0;

        $bulanA = isset($a['periode']) ? array_search($a['periode'], $urutanBulan) : -1;
        $bulanB = isset($b['periode']) ? array_search($b['periode'], $urutanBulan) : -1;

        return $tahunA <=> $tahunB ?: $bulanA <=> $bulanB;
    });

    // Default awal/akhir
    $periodeAwalBulan = '-';
    $periodeAwalTahun = '-';
    $periodeAkhirBulan = '-';
    $periodeAkhirTahun = '-';

    foreach ($data as $i => $row) {
        $aktual = $row['aktual'] ?? null;
        $hasilPrediksi = $row['hasil'] ?? null;

        if ($aktual !== null && $periodeAwalBulan === '-') {
            $periodeAwalBulan = $row['periode'] ?? '-';
            $periodeAwalTahun = $row['tahun'] ?? '-';
        }

        if ($hasilPrediksi === null || $aktual === null || $aktual == 0) continue;

        $error = $aktual - $hasilPrediksi;
        $totalMSE += pow($error, 2);
        $totalMAPE += abs($error / $aktual);
        $n++;

        // Update akhir (sementara)
        $periodeAkhirBulan = $row['periode'] ?? '-';
        $periodeAkhirTahun = $row['tahun'] ?? '-';
    }

    // Ambil akhir yang benar-benar punya aktual
    for ($i = count($data) - 1; $i >= 0; $i--) {
        if (isset($data[$i]['aktual']) && $data[$i]['aktual'] !== null) {
            $periodeAkhirBulan = $data[$i]['periode'] ?? '-';
            $periodeAkhirTahun = $data[$i]['tahun'] ?? '-';
            break;
        }
    }

    $mse = $n ? $totalMSE / $n : 0;
    $mape = $n ? ($totalMAPE / $n) * 100 : 0;

    // Prediksi bulan berikutnya
    $prediksiBulanBerikutnya = [
        'bulan' => '-',
        'tahun' => '-',
        'hasil' => 0,
    ];



    if (!empty($data)) {
        $lastIndex = count($data) - 1;
        $lastRow = $data[$lastIndex];
        $bulanTerakhir = $lastRow['periode'] ?? null;
        $tahunTerakhir = $lastRow['tahun'] ?? null;
        $prediksiTerakhir = $lastRow['hasil'] ?? null;

        if ($bulanTerakhir && $tahunTerakhir && $prediksiTerakhir !== null) {
            $indexBulan = array_search($bulanTerakhir, $urutanBulan);
            if ($indexBulan !== false) {
                $indexBulanSelanjutnya = $indexBulan + 1;

if ($indexBulanSelanjutnya >= 12) {
    $indexBulanSelanjutnya = 0;
    $tahunBerikutnya = $tahunTerakhir + 1;
} else {
    $tahunBerikutnya = $tahunTerakhir;
}

$bulanBerikutnya = $urutanBulan[$indexBulanSelanjutnya];
$tahunBerikutnya = $tahunTerakhir;

// Tambah ini sebelum assign ke $prediksiBulanBerikutnya
$nextIsForecastOnly = collect($data)->last()['aktual'] === null;
if (!$nextIsForecastOnly) {
    array_pop($data); // Hapus prediksi lebih dari 1 bulan ke depan jika ada
}

                $prediksiBulanBerikutnya = [
                    'bulan' => $bulanBerikutnya,
                    'tahun' => $tahunBerikutnya,
                    'hasil' => $prediksiTerakhir
                ];
            }
        }
    }

    // Simpan payload lengkap
    $payload = [
        'data' => $data,
        'bulan_berikutnya' => $prediksiBulanBerikutnya['bulan'],
        'tahun_berikutnya' => $prediksiBulanBerikutnya['tahun'],
        'peramalan_berikutnya' => $prediksiBulanBerikutnya['hasil'],
    ];

    // Simpan ke DB
    $pengujian = DataPengujian::create([
        'kategori'     => $item->kategori,
        'jenis_barang' => $item->jenis_barang,
        'tahun'        => $item->tahun,
        'mse'          => $mse,
        'mape'         => $mape,
        'data_json'    => json_encode($payload),
    ]);

    $akurasi = $this->getKategoriAkurasi($mape);

    // Kirim ke view hasil
    return view('ppic.pengujian.hasil', [
        'item' => $item,
        'data' => $data,
        'mse' => $mse / 1000000,
        'mape' => $mape,
        'bulanAwal' => $periodeAwalBulan,
        'tahunAwal' => $periodeAwalTahun,
        'bulanAkhir' => $periodeAkhirBulan,
        'tahunAkhir' => $periodeAkhirTahun,
        'bulanBerikutnya' => $prediksiBulanBerikutnya['bulan'],
        'tahunBerikutnya' => $prediksiBulanBerikutnya['tahun'],
        'hasilPeramalan' => $prediksiBulanBerikutnya['hasil'],
        'kategoriMap' => [
            'produksi' => 'Produksi',
            'bahan_baku' => 'Bahan Baku',
        ],
        'title' => 'Hasil Pengujian',
        'akurasi' => $akurasi,
    ]);
}


    public function riwayat()
    {
        $data = DataPengujian::orderBy('created_at', 'desc')->get();

        return view('ppic.pengujian.riwayat', [
            'data' => $data,
            'title' => 'Data Hasil Pengujian'
        ]);
    }

    public function detail($id)
{
    $item = DataPengujian::findOrFail($id);

    // Decode data_json (payload hasil perhitungan di method hitung)
    $payload = json_decode($item->data_json, true) ?? [];

    $data = $payload['data'] ?? [];
    $bulanBerikutnya = $payload['bulan_berikutnya'] ?? '-';
    $tahunBerikutnya = $payload['tahun_berikutnya'] ?? '-';
    $hasilPeramalan = $payload['peramalan_berikutnya'] ?? 0;

    // Tentukan akurasi dari nilai MAPE
    $akurasi = $this->getKategoriAkurasi($item->mape);

    // Tentukan periode awal dan akhir dari data aktual
    $periodeAwalBulan = '-';
    $periodeAwalTahun = '-';
    $periodeAkhirBulan = '-';
    $periodeAkhirTahun = '-';

    // Cari periode awal (data aktual pertama)
    foreach ($data as $row) {
        if (isset($row['aktual']) && $row['aktual'] !== null) {
            $periodeAwalBulan = $row['periode'] ?? '-';
            $periodeAwalTahun = $row['tahun'] ?? '-';
            break;
        }
    }

    // Cari periode akhir (data aktual terakhir)
    for ($i = count($data) - 1; $i >= 0; $i--) {
        if (isset($data[$i]['aktual']) && $data[$i]['aktual'] !== null) {
            $periodeAkhirBulan = $data[$i]['periode'] ?? '-';
            $periodeAkhirTahun = $data[$i]['tahun'] ?? '-';
            break;
        }
    }

    return view('ppic.pengujian.detail', [
    'item' => $item,
    'data' => $data,
    'mse' => $item->mse / 1000000,
    'mape' => $item->mape,
    'bulanAwal' => $periodeAwalBulan,
    'tahunAwal' => $periodeAwalTahun,
    'bulanAkhir' => $periodeAkhirBulan,
    'tahunAkhir' => $periodeAkhirTahun,
    'bulanBerikutnya' => $bulanBerikutnya,
    'tahunBerikutnya' => $tahunBerikutnya,
    'hasilPeramalan' => $hasilPeramalan,
    'akurasi' => $akurasi,
    'kategoriMap' => [
        'produksi' => 'Produksi',
        'bahan_baku' => 'Bahan Baku',
    ],
    'title' => 'Detail Hasil Pengujian'
]);
}



    public function destroy($id)
{
    // Cek apakah user yang login memiliki role PPIC
    if (auth()->user()->role !== 'ppic') {
        abort(403, 'Akses ditolak. Hanya PPIC yang boleh menghapus data.');
    }

    // Cari data pengujian berdasarkan ID
    $item = DataPengujian::findOrFail($id);

    // Hapus data tersebut dari database
    $item->delete();

    // Redirect kembali ke halaman riwayat dengan pesan sukses
    return redirect()->route('ppic.pengujian.riwayat')
                     ->with('success', 'Data pengujian berhasil dihapus.');
}

    public function download($id)
{
    $data = DataPengujian::findOrFail($id);

    $kategoriMap = [
        'produksi' => 'Data Produksi',
        'tambang' => 'Bahan Baku Tambang',
        'jaring' => 'Bahan Baku Jaring',
        'benang' => 'Bahan Baku Benang',
    ];

    $item = $data;
    $mse = $data->mse;
    $mape = $data->mape;

    $hasil = json_decode($data->data_json, true);
    $hasil = is_array($hasil) ? $hasil : [];

    $dataArray = $hasil['data'] ?? [];

    $periodeAwal = '-';
    $periodeAkhir = '-';
    $bulanBerikutnya = $hasil['bulan_berikutnya'] ?? '-';
    $tahunBerikutnya = $hasil['tahun_berikutnya'] ?? '-';
    $hasilPeramalan = $hasil['peramalan_berikutnya'] ?? 0;

    if (!empty($dataArray)) {
        $first = reset($dataArray);
        $last = end($dataArray);
        $periodeAwal = ($first['periode'] ?? $first['bulan'] ?? '-') . ' ' . ($first['tahun'] ?? '-');
        $periodeAkhir = ($last['periode'] ?? $last['bulan'] ?? '-') . ' ' . ($last['tahun'] ?? '-');
    }

    $pdf = Pdf::loadView('ppic.pengujian.pdf', [
        'data' => $dataArray,
        'item' => $item,
        'mse' => $mse/1000000,
        'mape' => $mape,
        'kategoriMap' => $kategoriMap,
        'periodeAwal' => $periodeAwal,
        'periodeAkhir' => $periodeAkhir,
        'bulanBerikutnya' => $bulanBerikutnya,
        'tahunBerikutnya' => $tahunBerikutnya,
        'hasilPeramalan' => $hasilPeramalan,
    ]);

    return $pdf->stream('laporan_pengujian_ppic.pdf');
}



public function indexManajer()
{
    $data = DataPengujian::all();

    $kategoriMap = [
        'produksi' => 'Data Produksi',
        'tambang' => 'Bahan Baku Tambang',
        'jaring' => 'Bahan Baku Jaring',
        'benang' => 'Bahan Baku Benang',
    ];

    return view('manajer.pengujian.index', [
        'data' => $data,
        'kategoriMap' => $kategoriMap,
        'title' => 'Data Hasil Pengujian'
    ]);
}

public function detailManajer($id)
{
    $item = DataPengujian::findOrFail($id);

    // Decode data_json (payload hasil perhitungan di method hitung)
    $payload = json_decode($item->data_json, true) ?? [];

    $data = $payload['data'] ?? [];
    $bulanBerikutnya = $payload['bulan_berikutnya'] ?? '-';
    $tahunBerikutnya = $payload['tahun_berikutnya'] ?? '-';
    $hasilPeramalan = $payload['peramalan_berikutnya'] ?? 0;

    // Tentukan akurasi dari nilai MAPE
   $akurasi = $this->getKategoriAkurasi($item->mape);

    // Tentukan periode awal dan akhir dari data aktual
    $periodeAwalBulan = '-';
    $periodeAwalTahun = '-';
    $periodeAkhirBulan = '-';
    $periodeAkhirTahun = '-';

    // Cari periode awal (data aktual pertama)
    foreach ($data as $row) {
        if (isset($row['aktual']) && $row['aktual'] !== null) {
            $periodeAwalBulan = $row['periode'] ?? '-';
            $periodeAwalTahun = $row['tahun'] ?? '-';
            break;
        }
    }

    // Cari periode akhir (data aktual terakhir)
    for ($i = count($data) - 1; $i >= 0; $i--) {
        if (isset($data[$i]['aktual']) && $data[$i]['aktual'] !== null) {
            $periodeAkhirBulan = $data[$i]['periode'] ?? '-';
            $periodeAkhirTahun = $data[$i]['tahun'] ?? '-';
            break;
        }
    }

    return view('manajer.pengujian.detail', [
    'item' => $item,
    'data' => $data,
    'mse' => $item->mse / 1000000,
    'mape' => $item->mape,
    'bulanAwal' => $periodeAwalBulan,
    'tahunAwal' => $periodeAwalTahun,
    'bulanAkhir' => $periodeAkhirBulan,
    'tahunAkhir' => $periodeAkhirTahun,
    'bulanBerikutnya' => $bulanBerikutnya,
    'tahunBerikutnya' => $tahunBerikutnya,
    'hasilPeramalan' => $hasilPeramalan,
    'akurasi' => $akurasi,
    'kategoriMap' => [
        'produksi' => 'Produksi',
        'bahan_baku' => 'Bahan Baku',
    ],
    'title' => 'Detail Hasil Pengujian'
]);
}



// DOWNLOAD PDF (jika tombol download diklik)
public function downloadManajer($id)
{
    $data = DataPengujian::findOrFail($id);

    $kategoriMap = [
        'produksi' => 'Data Produksi',
        'tambang' => 'Bahan Baku Tambang',
        'jaring' => 'Bahan Baku Jaring',
        'benang' => 'Bahan Baku Benang',
    ];

    $item = $data;
    $mse = $data->mse;
    $mape = $data->mape;

    $hasil = json_decode($data->data_json, true);
    $hasil = is_array($hasil) ? $hasil : [];

    $dataArray = $hasil['data'] ?? [];

    $periodeAwal = '-';
    $periodeAkhir = '-';
    $bulanBerikutnya = $hasil['bulan_berikutnya'] ?? '-';
    $tahunBerikutnya = $hasil['tahun_berikutnya'] ?? '-';
    $hasilPeramalan = $hasil['peramalan_berikutnya'] ?? 0;

    if (!empty($dataArray)) {
        $first = reset($dataArray);
        $last = end($dataArray);
        $periodeAwal = ($first['periode'] ?? $first['bulan'] ?? '-') . ' ' . ($first['tahun'] ?? '-');
        $periodeAkhir = ($last['periode'] ?? $last['bulan'] ?? '-') . ' ' . ($last['tahun'] ?? '-');
    }

    $pdf = Pdf::loadView('ppic.pengujian.pdf', [
        'data' => $dataArray,
        'item' => $item,
        'mse' => $mse/1000000,
        'mape' => $mape,
        'kategoriMap' => $kategoriMap,
        'periodeAwal' => $periodeAwal,
        'periodeAkhir' => $periodeAkhir,
        'bulanBerikutnya' => $bulanBerikutnya,
        'tahunBerikutnya' => $tahunBerikutnya,
        'hasilPeramalan' => $hasilPeramalan,
    ]);

    return $pdf->stream('laporan_pengujian_manajer.pdf');
}
}
