<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Penjualan;

#[Layout('layouts.app', ['title' => 'Detail Penjualan'])]
class PenjualanShow extends Component
{
    public Penjualan $penjualan;

    public function mount(Penjualan $penjualan): void
    {
        $this->penjualan = $penjualan->load(['items.produk:id,nama,sku']);
    }

    public function render()
    {
        return view('livewire.sales.penjualan-show');
    }
}
