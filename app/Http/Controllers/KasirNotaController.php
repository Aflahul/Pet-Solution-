<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class KasirNotaController extends Controller
{
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['items.produk:id,nama,sku', 'items']); // asumsikan relasi items->produk ada
        return view('livewire.kasir.nota', compact('penjualan'));

    }
}
