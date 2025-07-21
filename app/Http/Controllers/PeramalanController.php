<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\BahanBaku;
use App\Models\DataPeramalan;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilPeramalan;

class PeramalanController extends Controller
{
    public function index()
    {
        $tahunProduksi = Produksi::select('tahun')->distinct()->pluck('tahun')->toArray();
        $tahunBahanBaku = BahanBaku::select('tahun')->distinct()->pluck('tahun')->toArray();
        $daftarTahun = array_unique(array_merge($tahunProduksi, $tahunBahanBaku));
        sort($daftarTahun);

        return view('ppic.peramalan.index', [
            'daftarTahun' => $daftarTahun,
            'title' => 'Peramalan Data'
        ]);
    }
public function proses(Request $request)
{
    $kategori = $request->kategori;
    $jenis_barang = $request->jenis_barang;
    $tahun = $request->tahun;

    if ($kategori == 'produksi') {
        $data = Produksi::where('kategori', $jenis_barang)
            ->whereIn('tahun', (array) $tahun)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get(['bulan', 'tahun', 'jumlah_produksi as nilai']);
    } else {
        $data = BahanBaku::where('kategori', $jenis_barang)
            ->whereIn('tahun', (array) $tahun)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get(['bulan', 'tahun', 'jumlah_bahanbaku as nilai']);
    }

    if ($data->isEmpty()) {
        return back()->with('error', 'Data tidak ditemukan untuk parameter yang dipilih.');
    }

    $nilaiArray = $data->pluck('nilai')->toArray();
    $min = min($nilaiArray);
    $max = max($nilaiArray);
    $jumlahData = count($data);
    $jumlahTahun = $jumlahData / 12;

    if ($jumlahTahun <= 1) {
        $jumlah_interval = 4;
    } elseif ($jumlahTahun <= 2) {
        $jumlah_interval = 6;
    } elseif ($jumlahTahun <= 3) {
        $jumlah_interval = 7;
    } else {
        $jumlah_interval = round(sqrt($jumlahData));
    }

    $min_d = min($nilaiArray);
    $max_d = max($nilaiArray);
    $panjang_interval = ($max_d - $min_d) / $jumlah_interval;

    $semesta = [
        'min' => $min,
        'max' => $max,
        'min_d' => $min_d,
        'max_d' => $max_d,
        'jumlah_interval' => $jumlah_interval,
        'panjang_interval' => $panjang_interval
    ];

    $fuzzySets = [];
    for ($i = 0; $i < $jumlah_interval; $i++) {
        $awal = $min_d + $i * $panjang_interval;
        $akhir = $min_d + ($i + 1) * $panjang_interval;

        if ($i == $jumlah_interval - 1) {
            $akhir = $max_d;
        }

        $fuzzySets[] = [
            'range' => "$awal - $akhir",
            'label' => 'A' . ($i + 1),
            'start' => $awal,
            'end' => $akhir
        ];
    }

    $fuzzifikasi = [];
    foreach ($data as $item) {
        $nilai = $item->nilai;
        $fuzzy = '';

        foreach ($fuzzySets as $index => $set) {
            $awal = $set['start'];
            $akhir = $set['end'];

            if ($index == count($fuzzySets) - 1) {
                if ($nilai >= $awal && $nilai <= $akhir) {
                    $fuzzy = $set['label'];
                    break;
                }
            } else {
                if ($nilai >= $awal && $nilai < $akhir) {
                    $fuzzy = $set['label'];
                    break;
                }
            }
        }

        $fuzzifikasi[] = [
            'bulan' => $item->bulan,
            'tahun' => $item->tahun,
            'nilai' => $nilai,
            'fuzzy' => $fuzzy
        ];
    }

    $urutanBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $fuzzifikasi = collect($fuzzifikasi)->sortBy(function ($item) use ($urutanBulan) {
    return ($item['tahun'] * 100) + array_search($item['bulan'], $urutanBulan);
})->values()->all();

    $flr = [];
    for ($i = 0; $i < count($fuzzifikasi) - 1; $i++) {
        $from = $fuzzifikasi[$i]['fuzzy'];
        $to = $fuzzifikasi[$i + 1]['fuzzy'];
        $flr[] = [
            'periode' => $fuzzifikasi[$i + 1]['bulan'],
            'relasi' => "$from → $to"
        ];
    }

    $flrg = [];
    foreach ($flr as $relasi) {
        [$from, $to] = explode(' → ', $relasi['relasi']);
        $flrg[$from][] = $to;
    }

    uksort($flrg, function ($a, $b) {
        return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) - (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
    });

    $labelToMidValue = [];
    foreach ($fuzzySets as $set) {
        [$awal, $akhir] = explode(' - ', $set['range']);
        $labelToMidValue[$set['label']] = ($awal + $akhir) / 2;
    }

    $defuzzifikasi = [];
    for ($i = 0; $i < count($fuzzifikasi) - 1; $i++) {
        $currentLabel = $fuzzifikasi[$i]['fuzzy'];
        $nextPeriode = $fuzzifikasi[$i + 1]['bulan'];
        $actual = $fuzzifikasi[$i + 1]['nilai'];

        if (isset($flrg[$currentLabel])) {
            $sum = 0;
            $count = 0;
            foreach ($flrg[$currentLabel] as $toLabel) {
                if (isset($labelToMidValue[$toLabel])) {
                    $sum += $labelToMidValue[$toLabel];
                    $count++;
                }
            }
            $forecast = $count ? round(($sum / $count), 2) : null;
        } else {
            $forecast = null;
        }

        $defuzzifikasi[] = [
            'periode' => $nextPeriode,
            'aktual' => $actual,
            'hasil' => $forecast
        ];
    }

    // ✅ Tambahkan prediksi untuk bulan berikutnya
    $jumlahTahunRamalan = 2;
    $totalRamalan = $jumlahTahunRamalan * 12;
    $last = end($fuzzifikasi);
    $lastFuzzy = $last['fuzzy'];
    $lastBulanIndex = array_search($last['bulan'], $urutanBulan);
    $nextTahun = (int) $last['tahun'];

    for ($i = 0; $i < ($totalRamalan - (count($fuzzifikasi) - 1)); $i++) {
        $nextBulanIndex = ($lastBulanIndex + 1) % 12;
        if ($nextBulanIndex === 0) $nextTahun++;
        $nextBulan = $urutanBulan[$nextBulanIndex];

        if (isset($flrg[$lastFuzzy])) {
            $sum = 0;
            $count = 0;
            foreach ($flrg[$lastFuzzy] as $toLabel) {
                $sum += $labelToMidValue[$toLabel] ?? 0;
                $count++;
            }
            $forecast = $count ? round($sum / $count, 2) : null;

            foreach ($fuzzySets as $set) {
                if ($forecast >= $set['start'] && $forecast <= $set['end']) {
                    $lastFuzzy = $set['label'];
                    break;
                }
            }
        } else {
            $forecast = null;
        }

        $defuzzifikasi[] = [
            'periode' => $nextBulan,
            'aktual' => null,
            'hasil' => $forecast,
            'tahun' => $nextTahun
        ];

        $lastBulanIndex = $nextBulanIndex;
    }

    $defuzzifikasi = collect($defuzzifikasi)->values()->all();

    DataPeramalan::create([
        'kategori' => $kategori,
        'jenis_barang' => $jenis_barang,
        'tahun' => json_encode($tahun),
        'hasil_peramalan' => json_encode([
            'semesta' => $semesta,
            'fuzzySets' => $fuzzySets,
            'fuzzifikasi' => $fuzzifikasi,
            'flr' => $flr,
            'flrg' => $flrg,
            'defuzzifikasi' => $defuzzifikasi,
        ]),
    ]);

    $jumlahBulanPerTahun = 12;
    $tahunList = is_array($tahun) ? $tahun : [$tahun];

    foreach ($defuzzifikasi as $index => $item) {
        $tahunIndex = floor($index / $jumlahBulanPerTahun);
        $tahunAktual = $item['tahun'] ?? ($tahunList[$tahunIndex] ?? end($tahunList));

        HasilPeramalan::create([
            'kategori' => ucfirst($kategori),
            'jenis_barang' => ucfirst($jenis_barang),
            'tahun' => $tahunAktual,
            'bulan' => $item['periode'],
            'aktual' => $item['aktual'],
            'hasil' => $item['hasil'],
        ]);
    }

    return view('ppic.peramalan.hasil', compact(
        'kategori', 'jenis_barang', 'tahun',
        'semesta', 'fuzzySets', 'fuzzifikasi', 'flr', 'flrg', 'defuzzifikasi'
    ))->with('title', 'Hasil Peramalan Fuzzy Time Series');
}

   public function riwayat()
{
    $dataPeramalan = DataPeramalan::latest()->get();

    return view('ppic.peramalan.history', [
        'dataPeramalan' => $dataPeramalan,
        'title' => 'Riwayat Peramalan'
    ]);

    $dataPeramalan = DataPeramalan::latest()->get();

return view('manajer.peramalan.index', [
    'title' => 'Data Peramalan',
    'dataPeramalan' => $dataPeramalan,
]);

}


    public function detail($id)
    {
        $item = DataPeramalan::findOrFail($id);
        $data = json_decode($item->hasil_peramalan, true);

        return view('ppic.peramalan.detail', [
            'item' => $item,
            'semesta' => $data['semesta'],
            'fuzzySets' => $data['fuzzySets'],
            'fuzzifikasi' => $data['fuzzifikasi'],
            'flr' => $data['flr'],
            'flrg' => $data['flrg'],
            'defuzzifikasi' => $data['defuzzifikasi'],
            'title' => 'Detail Peramalan'
        ]);
    }

    public function download($id)
    {
        $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    $pdf = Pdf::loadView('ppic.peramalan.pdf', [
        'item' => $item,
        'data' => $data,
    ]);

    $filename = 'Peramalan_' . $item->kategori . '_' . $item->jenis_barang . '_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->stream($filename);
    }

    public function preview($id)
{
    $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    return view('ppic.peramalan.pdf-preview', [
        'item' => $item,
        'data' => $data,
        'title' => 'Preview Laporan Peramalan'
    ]);
}

    public function delete($id)
    {
        $item = DataPeramalan::findOrFail($id);
        $item->delete();

        return redirect()->route('peramalan.history')->with('success', 'Data peramalan berhasil dihapus.');
    }



public function indexManajer()
{
    $dataPeramalan = DataPeramalan::latest()->get();

    return view('manajer.peramalan.index', [
        'title' => 'Data Peramalan',
        'dataPeramalan' => $dataPeramalan
    ]);
}


public function detailManajer($id)
{
    $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    return view('manajer.peramalan.detail', [
        'item' => $item,
        'semesta' => $data['semesta'],
        'fuzzySets' => $data['fuzzySets'],
        'fuzzifikasi' => $data['fuzzifikasi'],
        'flr' => $data['flr'],
        'flrg' => $data['flrg'],
        'defuzzifikasi' => $data['defuzzifikasi'],
        'title' => 'Detail Peramalan'
    ]);
}

// PREVIEW PDF di browser (tampilan sebelum download)
public function previewManajer($id)
{
    $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    return view('manajer.peramalan.pdf-preview', [
        'item' => $item,
        'data' => $data,
        'title' => 'Preview Laporan Peramalan'
    ]);
}

// DOWNLOAD PDF (jika tombol download diklik)
public function downloadManajer($id)
{
    $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    $pdf = PDF::loadView('manajer.peramalan.pdf', compact('item', 'data'));

    $filename = 'Peramalan_' . $item->kategori . '_' . $item->jenis_barang . '_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->stream($filename); // pakai stream untuk preview, pakai ->download() kalau mau langsung download
}
}
