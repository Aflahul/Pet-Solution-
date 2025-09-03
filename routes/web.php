<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Master\KategoriIndex;
use App\Livewire\Master\SatuanIndex;
use App\Livewire\Master\ProdukIndex;
use App\Livewire\Kasir\TransaksiKasir;
use App\Http\Controllers\KasirNotaController;
use App\Livewire\Purchase\PembelianIndex;
use App\Livewire\Master\PemasokIndex;
use App\Livewire\Master\PemasokProdukMap;
use App\Livewire\Purchase\PembelianList;
use App\Livewire\Purchase\PembelianShow;

Route::get('/pembelian/list', PembelianList::class)->name('pembelian.list');
Route::get('/pembelian/{pembelian}', PembelianShow::class)->name('pembelian.show');
Route::get('/master/pemasok/{pemasok}/produk', PemasokProdukMap::class)
     ->name('master.pemasok.produk');
Route::get('/master/pemasok', PemasokIndex::class)->name('master.pemasok');
Route::get('/pembelian', PembelianIndex::class)->name('pembelian.index');
Route::get('/kasir', TransaksiKasir::class)->name('kasir.index');
Route::get('/kasir/nota/{penjualan}', [KasirNotaController::class, 'show'])
    ->name('kasir.nota');
Route::get('/master/produk', ProdukIndex::class)->name('produk.index');
Route::get('/master/kategori', KategoriIndex::class)->name('kategori.index');
Route::get('/master/satuan',   SatuanIndex::class)->name('satuan.index');

Route::get('/', function () {
    return view('welcome');
});
