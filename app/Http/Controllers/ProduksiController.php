<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;

class ProduksiController extends Controller

// ==== TAMBANG ====
{
    public function indexTambang()
    {
        $data = Produksi::where('kategori', 'tambang')->orderBy('tahun', 'desc')->get();
        return view('ppic.produksi.tambang.index', [
    'data' => $data,
    'title' => 'Produksi Tambang'
]);
    }

    public function createTambang()
    {
        return view('ppic.produksi.tambang.create', ['title' => 'Produksi Tambang']);
    }

    public function storeTambang(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric',
            'jumlah_produksi' => 'required|numeric'
        ]);

        Produksi::create([
            'kategori' => 'tambang',
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jumlah_produksi' => $request->jumlah_produksi
        ]);

        return redirect()->route('produksi.tambang.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function editTambang($id)
{
    $data = Produksi::findOrFail($id);
    return view('ppic.produksi.tambang.edit', [
        'data' => $data,
        'title' => 'Edit Produksi Tambang' // <-- ini wajib biar gak error
    ]);
}

public function updateTambang(Request $request, $id)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required|numeric',
        'jumlah_produksi' => 'required'
    ]);

    $data = Produksi::findOrFail($id);
    $data->update([
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'jumlah_produksi' => str_replace('.', '', $request->jumlah_produksi)
    ]);

    return redirect()->route('produksi.tambang.index')->with('success', 'Data berhasil diupdate');
}

public function deleteTambang($id)
{
    $data = Produksi::findOrFail($id);
    $data->delete();

    return redirect()->route('produksi.tambang.index')->with('success', 'Data berhasil dihapus');
}
// ==== JARING ====
public function indexJaring()
{
    $data = Produksi::where('kategori', 'jaring')->orderBy('tahun', 'desc')->get();
    return view('ppic.produksi.jaring.index', [
        'data' => $data,
        'title' => 'Produksi Jaring'
    ]);
}

public function createJaring()
{
    return view('ppic.produksi.jaring.create', ['title' => 'Produksi Jaring']);
}

public function storeJaring(Request $request)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required|numeric',
        'jumlah_produksi' => 'required|numeric'
    ]);

    Produksi::create([
        'kategori' => 'jaring',
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'jumlah_produksi' => $request->jumlah_produksi
    ]);

    return redirect()->route('produksi.jaring.index')->with('success', 'Data berhasil ditambahkan!');
}

public function editJaring($id)
{
    $data = Produksi::findOrFail($id);
    return view('ppic.produksi.jaring.edit', [
        'data' => $data,
        'title' => 'Edit Produksi Jaring'
    ]);
}

public function updateJaring(Request $request, $id)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required|numeric',
        'jumlah_produksi' => 'required'
    ]);

    $data = Produksi::findOrFail($id);
    $data->update([
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'jumlah_produksi' => str_replace('.', '', $request->jumlah_produksi)
    ]);

    return redirect()->route('produksi.jaring.index')->with('success', 'Data berhasil diupdate');
}

public function deleteJaring($id)
{
    $data = Produksi::findOrFail($id);
    $data->delete();

    return redirect()->route('produksi.jaring.index')->with('success', 'Data berhasil dihapus');
}

// ==== BENANG ====
public function indexBenang()
{
    $data = Produksi::where('kategori', 'benang')->orderBy('tahun', 'desc')->get();
    return view('ppic.produksi.benang.index', [
        'data' => $data,
        'title' => 'Produksi Benang'
    ]);
}

public function createBenang()
{
    return view('ppic.produksi.benang.create', ['title' => 'Produksi Benang']);
}

public function storeBenang(Request $request)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required|numeric',
        'jumlah_produksi' => 'required|numeric'
    ]);

    Produksi::create([
        'kategori' => 'benang',
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'jumlah_produksi' => $request->jumlah_produksi
    ]);

    return redirect()->route('produksi.benang.index')->with('success', 'Data berhasil ditambahkan!');
}

public function editBenang($id)
{
    $data = Produksi::findOrFail($id);
    return view('ppic.produksi.benang.edit', [
        'data' => $data,
        'title' => 'Edit Produksi Benang'
    ]);
}

public function updateBenang(Request $request, $id)
{
    $request->validate([
        'bulan' => 'required',
        'tahun' => 'required|numeric',
        'jumlah_produksi' => 'required'
    ]);

    $data = Produksi::findOrFail($id);
    $data->update([
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'jumlah_produksi' => str_replace('.', '', $request->jumlah_produksi)
    ]);

    return redirect()->route('produksi.benang.index')->with('success', 'Data berhasil diupdate');
}

public function deleteBenang($id)
{
    $data = Produksi::findOrFail($id);
    $data->delete();

    return redirect()->route('produksi.benang.index')->with('success', 'Data berhasil dihapus');
}

}
