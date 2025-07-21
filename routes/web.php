<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PPICController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajerController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengujianController;
use App\Http\Controllers\PeramalanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect ke login sebagai root
Route::get('/', fn () => redirect()->route('Login'));

// Auth Routes
Route::get('/login', [LoginController::class, 'index'])->name('Login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');

Route::get('/register', [RegisterController::class, 'index'])->name('Register');
Route::post('/register', [RegisterController::class, 'store'])->name('Register');

// ========================== PPIC ==========================
Route::middleware(['auth', 'role:ppic'])->group(function () {
    Route::get('/ppic/dashboard', [PPICController::class, 'dashboard'])->name('ppic.dashboard');

    // PRODUKSI
    Route::prefix('ppic/produksi')->group(function () {
        Route::prefix('tambang')->name('produksi.tambang.')->group(function () {
            Route::get('/', [ProduksiController::class, 'indexTambang'])->name('index');
            Route::get('/create', [ProduksiController::class, 'createTambang'])->name('create');
            Route::post('/store', [ProduksiController::class, 'storeTambang'])->name('store');
            Route::get('/edit/{id}', [ProduksiController::class, 'editTambang'])->name('edit');
            Route::put('/update/{id}', [ProduksiController::class, 'updateTambang'])->name('update');
            Route::delete('/delete/{id}', [ProduksiController::class, 'deleteTambang'])->name('delete');
        });

        Route::prefix('jaring')->name('produksi.jaring.')->group(function () {
            Route::get('/', [ProduksiController::class, 'indexJaring'])->name('index');
            Route::get('/create', [ProduksiController::class, 'createJaring'])->name('create');
            Route::post('/store', [ProduksiController::class, 'storeJaring'])->name('store');
            Route::get('/edit/{id}', [ProduksiController::class, 'editJaring'])->name('edit');
            Route::put('/update/{id}', [ProduksiController::class, 'updateJaring'])->name('update');
            Route::delete('/delete/{id}', [ProduksiController::class, 'deleteJaring'])->name('delete');
        });

        Route::prefix('benang')->name('produksi.benang.')->group(function () {
            Route::get('/', [ProduksiController::class, 'indexBenang'])->name('index');
            Route::get('/create', [ProduksiController::class, 'createBenang'])->name('create');
            Route::post('/store', [ProduksiController::class, 'storeBenang'])->name('store');
            Route::get('/edit/{id}', [ProduksiController::class, 'editBenang'])->name('edit');
            Route::put('/update/{id}', [ProduksiController::class, 'updateBenang'])->name('update');
            Route::delete('/delete/{id}', [ProduksiController::class, 'deleteBenang'])->name('delete');
        });
    });

    // BAHAN BAKU
    Route::prefix('ppic/bahanbaku')->group(function () {
        Route::prefix('tambang')->name('bahanbaku.tambang.')->group(function () {
            Route::get('/', [BahanBakuController::class, 'indexTambang'])->name('index');
            Route::get('/create', [BahanBakuController::class, 'createTambang'])->name('create');
            Route::post('/store', [BahanBakuController::class, 'storeTambang'])->name('store');
            Route::get('/edit/{id}', [BahanBakuController::class, 'editTambang'])->name('edit');
            Route::put('/update/{id}', [BahanBakuController::class, 'updateTambang'])->name('update');
            Route::delete('/delete/{id}', [BahanBakuController::class, 'deleteTambang'])->name('delete');
        });

        Route::prefix('jaring')->name('bahanbaku.jaring.')->group(function () {
            Route::get('/', [BahanBakuController::class, 'indexJaring'])->name('index');
            Route::get('/create', [BahanBakuController::class, 'createJaring'])->name('create');
            Route::post('/store', [BahanBakuController::class, 'storeJaring'])->name('store');
            Route::get('/edit/{id}', [BahanBakuController::class, 'editJaring'])->name('edit');
            Route::put('/update/{id}', [BahanBakuController::class, 'updateJaring'])->name('update');
            Route::delete('/delete/{id}', [BahanBakuController::class, 'deleteJaring'])->name('delete');
        });

        Route::prefix('benang')->name('bahanbaku.benang.')->group(function () {
            Route::get('/', [BahanBakuController::class, 'indexBenang'])->name('index');
            Route::get('/create', [BahanBakuController::class, 'createBenang'])->name('create');
            Route::post('/store', [BahanBakuController::class, 'storeBenang'])->name('store');
            Route::get('/edit/{id}', [BahanBakuController::class, 'editBenang'])->name('edit');
            Route::put('/update/{id}', [BahanBakuController::class, 'updateBenang'])->name('update');
            Route::delete('/delete/{id}', [BahanBakuController::class, 'deleteBenang'])->name('delete');
        });
    });

    // PERAMALAN (PPIC)
    Route::prefix('ppic')->group(function () {
        Route::get('/peramalan', [PeramalanController::class, 'index'])->name('peramalan.index');
        Route::post('/peramalan/proses', [PeramalanController::class, 'proses'])->name('peramalan.proses');
        Route::post('/peramalan/simpan', [PeramalanController::class, 'simpan'])->name('peramalan.simpan');
    });

    // RIWAYAT PERAMALAN (PPIC)
    Route::get('/riwayat-peramalan', [PeramalanController::class, 'riwayat'])->name('peramalan.history');
    Route::get('/riwayat-peramalan/{id}', [PeramalanController::class, 'detail'])->name('peramalan.detail');
    Route::get('/riwayat-peramalan/{id}/preview', [PeramalanController::class, 'preview'])->name('peramalan.preview');
    Route::get('/riwayat-peramalan/{id}/download', [PeramalanController::class, 'download'])->name('peramalan.download');
    Route::delete('/riwayat-peramalan/{id}', [PeramalanController::class, 'delete'])->name('peramalan.delete');
});

    Route::middleware(['auth', 'role:ppic'])->prefix('ppic')->group(function () {
    Route::get('/pengujian', [PengujianController::class, 'index'])->name('ppic.pengujian.index');
    Route::post('/pengujian/hitung', [PengujianController::class, 'hitung'])->name('ppic.pengujian.hitung');
    Route::get('/pengujian/riwayat', [PengujianController::class, 'riwayat'])->name('ppic.pengujian.riwayat');
    Route::get('/pengujian/{id}/detail', [PengujianController::class, 'detail'])->name('ppic.pengujian.detail');
    Route::get('/pengujian/{id}/download', [PengujianController::class, 'download'])->name('ppic.pengujian.download');
    Route::delete('/pengujian/{id}', [PengujianController::class, 'destroy'])->name('ppic.pengujian.destroy');
});



// ========================== MANAJER ==========================
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::get('/manajer/dashboard', [ManajerController::class, 'dashboard'])->name('manajer.dashboard');
    Route::get('/manajer/bahan-baku', [ManajerController::class, 'bahanBaku'])->name('manajer.bahanbaku');

    // Peramalan (MANAJER)
    Route::get('/manajer/peramalan', [PeramalanController::class, 'indexManajer'])->name('manajer.peramalan.index');
    Route::get('/manajer/riwayat-peramalan/{id}', [PeramalanController::class, 'detail'])->name('peramalan.detail.manajer');
    Route::get('/manajer/download-peramalan/{id}', [PeramalanController::class, 'download'])->name('peramalan.download.manajer');
    Route::delete('/manajer/hapus-peramalan/{id}', [PeramalanController::class, 'delete'])->name('peramalan.delete.manajer');

    // Pengujian
    Route::post('/pengujian/hitung', [PengujianController::class, 'hitung'])->name('pengujian.hitung');
    Route::get('/pengujian/riwayat', [PengujianController::class, 'riwayat'])->name('pengujian.riwayat');
    Route::get('/pengujian/{id}/detail', [PengujianController::class, 'detail'])->name('pengujian.detail');
    Route::get('/pengujian/{id}/download', [PengujianController::class, 'download'])->name('pengujian.download');
    Route::delete('/pengujian/{id}/hapus', [PengujianController::class, 'destroy'])->name('pengujian.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/manajer/produksi', [ManajerController::class, 'produksi'])->name('manajer.produksi');
});

Route::prefix('manajer/peramalan')->name('manajer.peramalan.')->group(function () {
    Route::get('/', [PeramalanController::class, 'indexManajer'])->name('index');
    Route::get('/detail/{id}', [PeramalanController::class, 'detailManajer'])->name('detail');
    Route::get('/download/{id}', [PeramalanController::class, 'downloadManajer'])->name('download');
});
// Route untuk manajer - Pengujian
Route::prefix('manajer')->middleware(['auth', 'role:manajer'])->group(function () {
    Route::get('/pengujian', [PengujianController::class, 'indexManajer'])->name('pengujian.manajer.index');
    Route::get('/pengujian/{id}/detail', [PengujianController::class, 'detailManajer'])->name('manajer.pengujian.detail');
    Route::get('/pengujian/{id}/download', [PengujianController::class, 'downloadManajer'])->name('manajer.pengujian.download');
    Route::get('/pengujian/{id}/preview', [PengujianController::class, 'previewManajer'])->name('manajer.pengujian.preview');
});
