<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeramalan;
use App\Models\DataPengujian;
use Barryvdh\DomPDF\Facade\Pdf;

class PengujianController extends Controller
{
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

        foreach ($data as $row) {
            $aktual = $row['aktual'];
            $hasilPrediksi = $row['hasil'];

            if ($hasilPrediksi === null || $aktual == 0) continue;

            $error = $aktual - $hasilPrediksi;
            $totalMSE += pow($error, 2);
            $totalMAPE += abs($error / $aktual);
            $n++;
        }

        $mse = $n ? $totalMSE / $n : 0;
        $mape = $n ? ($totalMAPE / $n) * 100 : 0;

        // Simpan ke database sebagai riwayat (jangan encode tahun lagi!)
        $pengujian = DataPengujian::create([
            'kategori'     => $item->kategori,
            'jenis_barang' => $item->jenis_barang,
            'tahun'        => $item->tahun,
            'mse'          => $mse,
            'mape'         => $mape,
            'data_json'    => json_encode($data),
        ]);

        // Tampilkan hasil langsung (tanpa redirect)
        return view('ppic.pengujian.hasil', [
            'item' => $pengujian,
            'data' => $data,
            'mse'  => $mse/1000000,
            'mape' => $mape,
            'title' => 'Hasil Pengujian Peramalan'
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
        $data = json_decode($item->data_json, true) ?? [];

        return view('ppic.pengujian.detail', [
            'item' => $item,
            'data' => $data,
            'mse'  => $item->mse/1000000,
            'mape' => $item->mape,
            'title' => 'Detail Hasil Pengujian'
        ]);
    }

    public function destroy($id)
{
    if (auth()->user()->role !== 'ppic') {
        abort(403, 'Akses ditolak. Hanya PPIC yang boleh menghapus data.');
    }

    $item = DataPengujian::findOrFail($id);
    $item->delete();

    return redirect()->route('ppic.pengujian.riwayat')->with('success', 'Data pengujian berhasil dihapus.');
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

    // Ambil data hasil yang sudah disimpan oleh PPIC
    $hasil = json_decode($data->data_json, true);
    $hasil = is_array($hasil) ? $hasil : []; // fallback kalau null

    $pdf = Pdf::loadView('manajer.pengujian.pdf', [
        'data' => $hasil,
        'item' => $item,
        'mse' => $mse/1000000,
        'mape' => $mape,
        'kategoriMap' => $kategoriMap,
    ]);

    return $pdf->stream('laporan_pengujian_manajer.pdf');
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
    $pengujian = DataPengujian::findOrFail($id);

    $hasil = json_decode($pengujian->data_json, true); // GANTI dari ->hasil ke ->data_json

    return view('manajer.pengujian.detail', [
        'title' => 'Detail Hasil Pengujian',
        'pengujian' => $pengujian,
        'hasil' => is_array($hasil) ? $hasil : [], // biar aman
        'mse' => $pengujian->mse/1000000,
        'mape' => $pengujian->mape,
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
    $mse = $data->mse/1000000;
    $mape = $data->mape;

    // Ambil data hasil yang sudah disimpan oleh PPIC
    $hasil = json_decode($data->data_json, true);
    $hasil = is_array($hasil) ? $hasil : []; // fallback kalau null

    $pdf = Pdf::loadView('manajer.pengujian.pdf', [
        'data' => $hasil,
        'item' => $item,
        'mse' => $mse,
        'mape' => $mape,
        'kategoriMap' => $kategoriMap,
    ]);

    return $pdf->stream('laporan_pengujian_manajer.pdf');
}
}
