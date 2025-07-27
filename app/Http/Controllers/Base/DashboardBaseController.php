<?php

// app/Http/Controllers/Base/DashboardBaseController.php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\HasilPeramalan;
use App\Models\DataPeramalan;
use Illuminate\Http\Request;

class DashboardBaseController extends Controller
{

protected function getDataChart(Request $request)
{
    $tahun = $request->input('tahun');
    $kategori = $request->input('kategori');

    $query = DataPeramalan::query();

    if ($tahun) {
        $query->where('tahun', $tahun);
    }

    if ($kategori) {
        $query->where('kategori', $kategori);
    }

    $data = $query->get();

    $result = [];

    foreach ($data as $item) {
        // Ambil data JSON hasil_peramalan (array bulan)
        $dataPerBulan = json_decode($item->hasil_peramalan, true);

        if (!$dataPerBulan || !is_array($dataPerBulan)) continue;

        $label = $item->kategori . ' - ' . $item->jenis_barang;

        foreach ($dataPerBulan as $bulan => $jumlah) {
            $key = $bulan . ' ' . $item->tahun;
            $result[$label][$key] = $jumlah;
        }
    }

    // Format agar cocok ke Chart.js
    $datasets = [];
    $labels = [];

    // Kumpulkan semua label (bulan-tahun)
    foreach ($result as $dataSet) {
        foreach (array_keys($dataSet) as $labelBulan) {
            if (!in_array($labelBulan, $labels)) {
                $labels[] = $labelBulan;
            }
        }
    }

    sort($labels); // Urutkan label X (misalnya Januari 2022, dst)

    foreach ($result as $label => $dataSet) {
        $dataY = [];
        foreach ($labels as $labelBulan) {
            $dataY[] = $dataSet[$labelBulan] ?? null;
        }

        $datasets[] = [
            'label' => $label,
            'data' => $dataY,
            'borderWidth' => 2,
            'fill' => false,
        ];
    }

    return [
        'labels' => $labels,
        'datasets' => $datasets,
    ];
}

}
