<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\BahanBaku;
use App\Http\Controllers\Base\DashboardBaseController;

class ManajerController extends DashboardBaseController
{
    // Halaman dashboard manajer produksi
    public function dashboard()
    {
        $dataChart = $this->getDataChart();

        return view('manajer.dashboard', [
            'title' => 'Dashboard Manajer',
            'dataChart' => $dataChart
        ]);
    }

    // Halaman data produksi (versi awal tanpa filter dropdown)
    public function produksi()
{
    $tambang = Produksi::where('kategori', 'tambang')->get();
    $jaring = Produksi::where('kategori', 'jaring')->get();
    $benang = Produksi::where('kategori', 'benang')->get();

    return view('manajer.produksi', [
        'title' => 'Data Produksi',
        'tambang' => $tambang,
        'jaring' => $jaring,
        'benang' => $benang,
    ]);
}

public function bahanBaku()
{
    $tambang = BahanBaku::where('kategori', 'Tambang')->orderBy('tahun')->get();
    $jaring = BahanBaku::where('kategori', 'Jaring')->orderBy('tahun')->get();
    $benang = BahanBaku::where('kategori', 'Benang')->orderBy('tahun')->get();

    return view('manajer.bahanbaku', [
        'title' => 'Data Bahan Baku',
        'tambang' => $tambang,
        'jaring' => $jaring,
        'benang' => $benang
    ]);
}
}
