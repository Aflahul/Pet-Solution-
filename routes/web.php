<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Master\KategoriIndex;
use App\Livewire\Master\SatuanIndex;
use App\Livewire\Master\ProdukIndex;

Route::get('/master/produk', ProdukIndex::class)->name('produk.index');
Route::get('/master/kategori', KategoriIndex::class)->name('kategori.index');
Route::get('/master/satuan',   SatuanIndex::class)->name('satuan.index');

Route::get('/', function () {
    return view('welcome');
});
