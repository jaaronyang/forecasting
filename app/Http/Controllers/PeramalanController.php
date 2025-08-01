<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\BahanBaku;
use App\Models\DataPeramalan;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HasilPeramalan;
use Carbon\Carbon;

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

if ($jumlahData <= 13) {
    $jumlah_interval = 4;
} elseif ($jumlahData <= 25) {
    $jumlah_interval = 6;
} elseif ($jumlahData <= 37) {
    $jumlah_interval = 8;
} else {
    // Misal ingin tetap pakai rumus, bisa lanjutkan pola: 4 + floor(jumlahData / 12 - 1) * 2
    $jumlah_interval = 4 + (floor($jumlahData / 12) - 1) * 2;
}

    $min_d = min($nilaiArray);
    $max_d = max($nilaiArray);
    $panjang_interval = ($max_d - $min_d) / $jumlah_interval;
    $panjang_interval = round($panjang_interval, 2);

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

// Buat FLRG: setiap from bisa punya banyak to
foreach ($flr as $relasi) {
    [$from, $to] = explode(' → ', $relasi['relasi']);
    $flrg[$from][] = $to;
}

// Hilangkan duplikat dan urutkan isi relasi (value)
foreach ($flrg as $from => $tos) {
    // Buang duplikat
    $tos = array_unique($tos);
    // Urutkan isi relasi
    usort($tos, function ($a, $b) {
        return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
    });
    $flrg[$from] = $tos;
}

// Urutkan key berdasarkan angka di A1, A2, dst (bukan string biasa)
uksort($flrg, function ($a, $b) {
    return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
});


    $labelToMidValue = [];
foreach ($fuzzySets as $set) {
    [$awal, $akhir] = explode(' - ', $set['range']);

    // Konversi ke float agar tidak terjadi penjumlahan string
    $awal = (float) $awal;
    $akhir = (float) $akhir;

    // Hitung nilai tengah (midpoint)
    $labelToMidValue[$set['label']] = round(($awal + $akhir) / 2, 2);
}

    $defuzzifikasi = [];

// Hitung jumlah data aktual yang tersedia
$jumlahAktual = count($fuzzifikasi) - 1;

for ($i = 0; $i < $jumlahAktual; $i++) {
    $currentLabel = $fuzzifikasi[$i]['fuzzy'];
    $nextPeriode = $fuzzifikasi[$i + 1]['bulan'];
    $tahunPeriode = $fuzzifikasi[$i + 1]['tahun'];
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
        $forecast = $count ? round($sum / $count, 2) : null;
    } else {
        $forecast = null;
    }

    $defuzzifikasi[] = [
        'periode' => $nextPeriode,
        'tahun' => $tahunPeriode,
        'aktual' => $actual,
        'hasil' => $forecast
    ];
    }

    // ✅ Tambahkan prediksi untuk bulan berikutnya
    // ✅ Prediksi hanya 1 bulan ke depan
        $last = end($fuzzifikasi);
        $lastFuzzy = $last['fuzzy'];
        $lastBulanIndex = array_search($last['bulan'], $urutanBulan);
        $nextBulanIndex = ($lastBulanIndex + 1) % 12;
        $nextBulan = $urutanBulan[$nextBulanIndex];
        $nextTahun = (int) $last['tahun'] + ($nextBulanIndex === 0 ? 1 : 0);

        if (isset($flrg[$lastFuzzy])) {
            $sum = 0;
            $count = 0;
            foreach ($flrg[$lastFuzzy] as $toLabel) {
                $sum += $labelToMidValue[$toLabel] ?? 0;
                $count++;
            }
            $forecast = $count ? round($sum / $count, 2) : null;
        } else {
            $forecast = null;
        }

        $defuzzifikasi[] = [
            'periode' => $nextBulan,
            'aktual' => null,
            'hasil' => $forecast,
            'tahun' => $nextTahun
        ];

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

            if (!is_null($item['aktual'])) {
                HasilPeramalan::create([
                    'kategori' => ucfirst($kategori),
                    'jenis_barang' => ucfirst($jenis_barang),
                    'tahun' => $tahunAktual,
                    'bulan' => $item['periode'],
                    'aktual' => $item['aktual'],
                    'hasil' => $item['hasil'],
                ]);
            }
        }
    $hasilPeramalanTerakhir = end($defuzzifikasi);
    $item = (object)[
    'kategori' => $kategori,
    'jenis_barang' => $jenis_barang,
    'tahun' => $tahun
];
    $aktualValues = collect($defuzzifikasi)
    ->pluck('aktual')
    ->filter(fn($val) => !is_null($val))
    ->values();

$nilaiTerendah = $aktualValues->min();
$nilaiTertinggi = $aktualValues->max();
$jumlahData = $aktualValues->count();

$bulanAwal = $fuzzifikasi[0]['bulan'];
$tahunAwal = $fuzzifikasi[0]['tahun'];

$lastIndex = count($defuzzifikasi) - 2; // -2 karena yang terakhir biasanya untuk prediksi
$bulanAkhir = $defuzzifikasi[$lastIndex]['periode'] ?? '-';
$tahunAkhir = $defuzzifikasi[$lastIndex]['tahun'] ?? '-';

    return view('ppic.peramalan.hasil', compact(
        'kategori', 'jenis_barang', 'tahun',
        'semesta', 'fuzzySets', 'fuzzifikasi', 'flr', 'flrg', 'defuzzifikasi', 'hasilPeramalanTerakhir', 'item', 'nilaiTerendah', 'nilaiTertinggi', 'jumlahData',
    'bulanAwal', 'tahunAwal', 'bulanAkhir', 'tahunAkhir'
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
    $itemData = DataPeramalan::findOrFail($id);
    $data = json_decode($itemData->hasil_peramalan, true);

    $defuzzifikasi = $data['defuzzifikasi'] ?? [];

    // Ambil hanya nilai aktual yang tidak null
    $aktualValues = collect($defuzzifikasi)
        ->pluck('aktual')
        ->filter(fn($val) => !is_null($val))
        ->values();

    $nilaiTerendah = $aktualValues->min() ?? 0;
    $nilaiTertinggi = $aktualValues->max() ?? 0;
    $jumlahData = $aktualValues->count();

    // Ambil periode awal dari fuzzifikasi
    $fuzzifikasi = $data['fuzzifikasi'] ?? [];
    $bulanAwal = $fuzzifikasi[0]['bulan'] ?? '-';
    $tahunAwal = $fuzzifikasi[0]['tahun'] ?? '-';

    // Ambil periode akhir dari defuzzifikasi (sebelum prediksi)
    $lastIndex = count($defuzzifikasi) - 2; // -2 karena terakhir biasanya hasil prediksi
    $bulanAkhir = $defuzzifikasi[$lastIndex]['periode'] ?? '-';
    $tahunAkhir = $defuzzifikasi[$lastIndex]['tahun'] ?? '-';

    // Urutkan fuzzy sets (array asosiatif dengan key A1, A2, ...)
    $fuzzySets = $data['fuzzySets'] ?? [];
    uksort($fuzzySets, function ($a, $b) {
        return intval(substr($a, 1)) <=> intval(substr($b, 1));
    });

    // Urutkan FLRG berdasarkan key (misalnya A1, A2, ...)
    $flrg = $data['flrg'] ?? [];
    uksort($flrg, function ($a, $b) {
        return intval(substr($a, 1)) <=> intval(substr($b, 1));
    });

    // Buat ulang object item agar kompatibel dengan blade
    $item = (object)[
        'kategori' => $itemData->kategori,
        'jenis_barang' => $itemData->jenis_barang,
        'tahun' => $itemData->tahun,
    ];

    return view('ppic.peramalan.detail', [
        'item' => $item,
        'semesta' => $data['semesta'],
        'fuzzySets' => $fuzzySets,
        'fuzzifikasi' => $fuzzifikasi,
        'flr' => $data['flr'],
        'flrg' => $flrg,
        'defuzzifikasi' => $defuzzifikasi,
        'hasilPeramalanTerakhir' => end($defuzzifikasi),
        'bulanAwal' => $bulanAwal,
        'tahunAwal' => $tahunAwal,
        'bulanAkhir' => $bulanAkhir,
        'tahunAkhir' => $tahunAkhir,
        'jumlahData' => $jumlahData,
        'nilaiTerendah' => $nilaiTerendah,
        'nilaiTertinggi' => $nilaiTertinggi,
        'title' => 'Detail Peramalan'
    ]);
}


  public function download($id)
{
    $item = DataPeramalan::findOrFail($id);
    $data = json_decode($item->hasil_peramalan, true);

    // Ambil data awal dan akhir dari fuzzifikasi & defuzzifikasi
    $fuzzifikasi = $data['fuzzifikasi'] ?? [];
    $defuzzifikasi = $data['defuzzifikasi'] ?? [];

    // Ambil periode awal dari fuzzifikasi pertama
    $bulanAwal = $fuzzifikasi[0]['bulan'] ?? '-';
    $tahunAwal = $fuzzifikasi[0]['tahun'] ?? '-';

    // Ambil periode akhir dari defuzzifikasi terakhir (bukan hasil ramalan terakhir)
    $lastIndex = count($defuzzifikasi) - 2; // -2 karena terakhir biasanya hasil prediksi
    $bulanAkhir = $defuzzifikasi[$lastIndex]['periode'] ?? '-';
    $tahunAkhir = $defuzzifikasi[$lastIndex]['tahun'] ?? '-';

    // Ambil hasil peramalan terakhir
    $hasilPeramalanTerakhir = end($defuzzifikasi);

    // Ambil data aktual untuk menghitung statistik
    $aktual = array_filter(array_column($defuzzifikasi, 'aktual'));
    $jumlahData = count($aktual);
    $nilaiTerendah = $jumlahData > 0 ? min($aktual) : 0;
    $nilaiTertinggi = $jumlahData > 0 ? max($aktual) : 0;

    // Urutkan fuzzy sets berdasarkan label (A1, A2, A3, ...)
    $fuzzySets = $data['fuzzySets'] ?? [];
    ksort($fuzzySets); // <--- TAMBAHKAN INI

    // Kirim data ke view PDF
    $pdf = Pdf::loadView('ppic.peramalan.pdf', [
        'item' => $item,
        'data' => $data,
        'fuzzifikasi' => $fuzzifikasi,
        'semesta' => $data['semesta'],
        'fuzzySets' => $fuzzySets, // gunakan hasil yang sudah diurutkan
        'flr' => $data['flr'],
        'flrg' => $data['flrg'],
        'defuzzifikasi' => $defuzzifikasi,
        'hasilPeramalanTerakhir' => $hasilPeramalanTerakhir,

        // Variabel tambahan untuk kesimpulan
        'bulanAwal' => $bulanAwal,
        'tahunAwal' => $tahunAwal,
        'bulanAkhir' => $bulanAkhir,
        'tahunAkhir' => $tahunAkhir,
        'jumlahData' => $jumlahData,
        'nilaiTerendah' => $nilaiTerendah,
        'nilaiTertinggi' => $nilaiTertinggi,
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
    try {
        $item = DataPeramalan::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('peramalan.history')
            ->with('success', 'Data peramalan berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()
            ->route('peramalan.history')
            ->with('error', 'Terjadi kesalahan saat menghapus data.');
    }
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
    $itemData = DataPeramalan::findOrFail($id);
    $data = json_decode($itemData->hasil_peramalan, true);

    $defuzzifikasi = $data['defuzzifikasi'] ?? [];

    // Ambil hanya nilai aktual yang tidak null
    $aktualValues = collect($defuzzifikasi)
        ->pluck('aktual')
        ->filter(fn($val) => !is_null($val))
        ->values();

    $nilaiTerendah = $aktualValues->min() ?? 0;
    $nilaiTertinggi = $aktualValues->max() ?? 0;
    $jumlahData = $aktualValues->count();

    // Ambil periode awal dari fuzzifikasi
    $fuzzifikasi = $data['fuzzifikasi'] ?? [];
    $bulanAwal = $fuzzifikasi[0]['bulan'] ?? '-';
    $tahunAwal = $fuzzifikasi[0]['tahun'] ?? '-';

    // Ambil periode akhir dari defuzzifikasi (sebelum prediksi)
    $lastIndex = count($defuzzifikasi) - 2; // -2 karena terakhir biasanya hasil prediksi
    $bulanAkhir = $defuzzifikasi[$lastIndex]['periode'] ?? '-';
    $tahunAkhir = $defuzzifikasi[$lastIndex]['tahun'] ?? '-';

    // Urutkan fuzzy sets (array asosiatif dengan key A1, A2, ...)
    $fuzzySets = $data['fuzzySets'] ?? [];
    uksort($fuzzySets, function ($a, $b) {
        return intval(substr($a, 1)) <=> intval(substr($b, 1));
    });

    // Urutkan FLRG berdasarkan key (misalnya A1, A2, ...)
    $flrg = $data['flrg'] ?? [];
    uksort($flrg, function ($a, $b) {
        return intval(substr($a, 1)) <=> intval(substr($b, 1));
    });

    // Buat ulang object item agar kompatibel dengan blade
    $item = (object)[
        'kategori' => $itemData->kategori,
        'jenis_barang' => $itemData->jenis_barang,
        'tahun' => $itemData->tahun,
    ];

    return view('manajer.peramalan.detail', [
        'item' => $item,
        'semesta' => $data['semesta'],
        'fuzzySets' => $fuzzySets,
        'fuzzifikasi' => $fuzzifikasi,
        'flr' => $data['flr'],
        'flrg' => $flrg,
        'defuzzifikasi' => $defuzzifikasi,
        'hasilPeramalanTerakhir' => end($defuzzifikasi),
        'bulanAwal' => $bulanAwal,
        'tahunAwal' => $tahunAwal,
        'bulanAkhir' => $bulanAkhir,
        'tahunAkhir' => $tahunAkhir,
        'jumlahData' => $jumlahData,
        'nilaiTerendah' => $nilaiTerendah,
        'nilaiTertinggi' => $nilaiTertinggi,
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

    // Ambil data awal dan akhir dari fuzzifikasi & defuzzifikasi
    $fuzzifikasi = $data['fuzzifikasi'] ?? [];
    $defuzzifikasi = $data['defuzzifikasi'] ?? [];

    // Ambil periode awal dari fuzzifikasi pertama
    $bulanAwal = $fuzzifikasi[0]['bulan'] ?? '-';
    $tahunAwal = $fuzzifikasi[0]['tahun'] ?? '-';

    // Ambil periode akhir dari defuzzifikasi terakhir (bukan hasil ramalan terakhir)
    $lastIndex = count($defuzzifikasi) - 2; // -2 karena terakhir biasanya hasil prediksi
    $bulanAkhir = $defuzzifikasi[$lastIndex]['periode'] ?? '-';
    $tahunAkhir = $defuzzifikasi[$lastIndex]['tahun'] ?? '-';

    // Ambil hasil peramalan terakhir
    $hasilPeramalanTerakhir = end($defuzzifikasi);

    // Ambil data aktual untuk menghitung statistik
    $aktual = array_filter(array_column($defuzzifikasi, 'aktual'));
    $jumlahData = count($aktual);
    $nilaiTerendah = $jumlahData > 0 ? min($aktual) : 0;
    $nilaiTertinggi = $jumlahData > 0 ? max($aktual) : 0;

    // Urutkan fuzzy sets berdasarkan label (A1, A2, A3, ...)
    $fuzzySets = $data['fuzzySets'] ?? [];
    ksort($fuzzySets); // <--- TAMBAHKAN INI

    // Kirim data ke view PDF
    $pdf = Pdf::loadView('ppic.peramalan.pdf', [
        'item' => $item,
        'data' => $data,
        'fuzzifikasi' => $fuzzifikasi,
        'semesta' => $data['semesta'],
        'fuzzySets' => $fuzzySets, // gunakan hasil yang sudah diurutkan
        'flr' => $data['flr'],
        'flrg' => $data['flrg'],
        'defuzzifikasi' => $defuzzifikasi,
        'hasilPeramalanTerakhir' => $hasilPeramalanTerakhir,

        // Variabel tambahan untuk kesimpulan
        'bulanAwal' => $bulanAwal,
        'tahunAwal' => $tahunAwal,
        'bulanAkhir' => $bulanAkhir,
        'tahunAkhir' => $tahunAkhir,
        'jumlahData' => $jumlahData,
        'nilaiTerendah' => $nilaiTerendah,
        'nilaiTertinggi' => $nilaiTertinggi,
    ]);

    $filename = 'Peramalan_' . $item->kategori . '_' . $item->jenis_barang . '_' . now()->format('Ymd_His') . '.pdf';
    return $pdf->stream($filename);
}
}
