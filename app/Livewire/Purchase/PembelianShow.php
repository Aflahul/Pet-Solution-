<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Pembelian;

#[Layout('layouts.app', ['title' => 'Detail Pembelian'])]
class PembelianShow extends Component
{
    public Pembelian $pembelian;

    public function mount(Pembelian $pembelian): void
    {
        $this->pembelian = $pembelian->load(['pemasok:id,nama','items.produk:id,nama,sku']);
    }

    public function render()
    {
        return view('livewire.purchase.pembelian-show');
    }
}
