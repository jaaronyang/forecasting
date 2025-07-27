<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataPeramalan;
use App\Http\Controllers\Base\DashboardBaseController;
use App\Models\Produksi;

class PPICController extends DashboardBaseController
{
    public function dashboard(Request $request)
{
    $selectedTahun = (array) $request->input('tahun', []);
    $kategori = $request->input('kategori');

    $query = DataPeramalan::query();

    if (!empty($selectedTahun)) {
        $query->whereIn('tahun', $selectedTahun);
    }

    if ($kategori) {
        $query->where('kategori', $kategori);
    }

    $records = $query->get();

    $dataChart = [];

    foreach ($records as $record) {
        $label = "{$record->kategori} - {$record->jenis_barang}";
        $hasilPeramalan = json_decode($record->hasil_peramalan, true);

        if (!isset($hasilPeramalan['defuzzifikasi'])) {
            continue;
        }

        $bulan = [];
        $aktual = [];
        $hasil = [];

        foreach ($hasilPeramalan['defuzzifikasi'] as $item) {
            $bulan[] = $item['periode'] ?? '-';
            $aktual[] = $item['aktual'] ?? 0;
            $hasil[] = $item['hasil'] ?? 0;
        }

        $dataChart[$label] = [
            'bulan' => $bulan,
            'aktual' => $aktual,
            'hasil' => $hasil,
        ];
    }

    $availableTahun = DataPeramalan::distinct()->pluck('tahun')->toArray();

    return view('ppic.dashboard', [
        'dataChart' => $dataChart,
        'availableTahun' => $availableTahun,
        'selectedTahun' => $selectedTahun,
        'kategori' => $kategori,
        'title' => 'PPIC Dashboard'
    ]);
}
public function produksi()
{
    $data = Produksi::orderBy('tahun', 'desc')->get();
    return view('manajer.produksi.index', [
        'data' => $data,
        'title' => 'Data Produksi Semua Kategori'
    ]);
}

}
