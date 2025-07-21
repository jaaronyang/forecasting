<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;

class BahanBakuController extends Controller
{
    // ==== TAMBANG ====
    public function indexTambang() {
        $data = BahanBaku::where('kategori', 'tambang')->orderBy('tahun', 'desc')->get();
        return view('ppic.bahanbaku.tambang.index', ['data' => $data, 'title' => 'Bahan Baku Tambang']);
    }

    public function createTambang() {
        return view('ppic.bahanbaku.tambang.create', ['title' => 'Tambah Bahan Baku Tambang']);
    }

    public function storeTambang(Request $request) {
        $this->validateBahanBaku($request);
        $this->simpanBahanBaku($request, 'tambang');
        return redirect()->route('bahanbaku.tambang.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function editTambang($id) {
        $data = BahanBaku::findOrFail($id);
        return view('ppic.bahanbaku.tambang.edit', ['data' => $data, 'title' => 'Edit Bahan Baku Tambang']);
    }

    public function updateTambang(Request $request, $id) {
        $this->validateBahanBaku($request);
        $this->updateBahanBaku($request, $id);
        return redirect()->route('bahanbaku.tambang.index')->with('success', 'Data berhasil diupdate');
    }

    public function deleteTambang($id) {
        BahanBaku::findOrFail($id)->delete();
        return redirect()->route('bahanbaku.tambang.index')->with('success', 'Data berhasil dihapus');
    }

    // ==== JARING ====
    public function indexJaring() {
        $data = BahanBaku::where('kategori', 'jaring')->orderBy('tahun', 'desc')->get();
        return view('ppic.bahanbaku.jaring.index', ['data' => $data, 'title' => 'Bahan Baku Jaring']);
    }

    public function createJaring() {
        return view('ppic.bahanbaku.jaring.create', ['title' => 'Tambah Bahan Baku Jaring']);
    }

    public function storeJaring(Request $request) {
        $this->validateBahanBaku($request);
        $this->simpanBahanBaku($request, 'jaring');
        return redirect()->route('bahanbaku.jaring.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function editJaring($id)
{
    $data = BahanBaku::findOrFail($id);
    return view('ppic.bahanbaku.jaring.edit', [
        'data' => $data,
        'title' => 'Edit Bahan Baku Jaring'
    ]);
}


    public function updateJaring(Request $request, $id) {
        $this->validateBahanBaku($request);
        $this->updateBahanBaku($request, $id);
        return redirect()->route('bahanbaku.jaring.index')->with('success', 'Data berhasil diupdate');
    }

    public function deleteJaring($id) {
        BahanBaku::findOrFail($id)->delete();
        return redirect()->route('bahanbaku.jaring.index')->with('success', 'Data berhasil dihapus');
    }

    // ==== BENANG ====
    public function indexBenang() {
        $data = BahanBaku::where('kategori', 'benang')->orderBy('tahun', 'desc')->get();
        return view('ppic.bahanbaku.benang.index', ['data' => $data, 'title' => 'Bahan Baku Benang']);
    }

    public function createBenang() {
        return view('ppic.bahanbaku.benang.create', ['title' => 'Tambah Bahan Baku Benang']);
    }

    public function storeBenang(Request $request) {
        $this->validateBahanBaku($request);
        $this->simpanBahanBaku($request, 'benang');
        return redirect()->route('bahanbaku.benang.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function editBenang($id) {
        $data = BahanBaku::findOrFail($id);
        return view('ppic.bahanbaku.benang.edit', ['data' => $data, 'title' => 'Edit Bahan Baku Benang']);
    }

    public function updateBenang(Request $request, $id) {
        $this->validateBahanBaku($request);
        $this->updateBahanBaku($request, $id);
        return redirect()->route('bahanbaku.benang.index')->with('success', 'Data berhasil diupdate');
    }

    public function deleteBenang($id) {
        BahanBaku::findOrFail($id)->delete();
        return redirect()->route('bahanbaku.benang.index')->with('success', 'Data berhasil dihapus');
    }

    // ==== FUNGSI BANTUAN ====
    private function validateBahanBaku(Request $request) {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required|numeric',
            'jumlah_bahanbaku' => 'required'
        ]);
    }

    private function simpanBahanBaku(Request $request, $kategori) {
        BahanBaku::create([
            'kategori' => $kategori,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jumlah_bahanbaku' => str_replace('.', '', $request->jumlah_bahanbaku)
        ]);
    }

    private function updateBahanBaku(Request $request, $id) {
        BahanBaku::findOrFail($id)->update([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jumlah_bahanbaku' => str_replace('.', '', $request->jumlah_bahanbaku)
        ]);
    }
}
