<?php

// app/Http/Controllers/Base/DashboardBaseController.php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\HasilPeramalan;

class DashboardBaseController extends Controller
{
    protected function getDataChart()
    {
        $dataPeramalan = HasilPeramalan::orderBy('tahun')->get();

        $dataChart = [];

        foreach ($dataPeramalan as $item) {
            $label = $item->kategori . ' - ' . $item->jenis_barang;

            if (!isset($dataChart[$label])) {
                $dataChart[$label] = [
                    'tahun' => [],
                    'aktual' => [],
                    'hasil' => [],
                ];
            }

            $dataChart[$label]['tahun'][] = $item->tahun;
            $dataChart[$label]['aktual'][] = $item->aktual;
            $dataChart[$label]['hasil'][] = $item->hasil;
        }

        return $dataChart;
    }
}
