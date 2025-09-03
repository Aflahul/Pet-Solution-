<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Penjualan;

#[Layout('layouts.app', ['title' => 'Daftar Penjualan'])]
class PenjualanList extends Component
{
    use WithPagination;

    // Filter & sort
    public string $cari = '';
    public ?int $kasir_id = null;
    public string $metode = ''; // ''=semua, 'TUNAI' atau 'NON_TUNAI'
    public string $tgl_awal;
    public string $tgl_akhir;
    public string $sortField = 'tanggal';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    public function mount(): void
    {
        $this->tgl_akhir = now()->toDateString();
        $this->tgl_awal  = now()->subDays(30)->toDateString();
    }

    public function updatingCari(){ $this->resetPage(); }
    public function updatedKasirId(){ $this->resetPage(); }
    public function updatedMetode(){ $this->resetPage(); }
    public function updatedTglAwal(){ $this->resetPage(); }
    public function updatedTglAkhir(){ $this->resetPage(); }
    public function updatedPerPage(){ $this->resetPage(); }

    public function sortBy(string $f): void
    {
        $this->sortDir = $this->sortField === $f ? ($this->sortDir === 'asc' ? 'desc' : 'asc') : 'asc';
        $this->sortField = $f;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->cari = '';
        $this->kasir_id = null;
        $this->metode = '';
        $this->tgl_akhir = now()->toDateString();
        $this->tgl_awal  = now()->subDays(30)->toDateString();
        $this->resetPage();
    }

    public function render()
    {
        $s = trim($this->cari);

        $base = Penjualan::query()
            ->when($this->tgl_awal && $this->tgl_akhir, fn($q) =>
                $q->whereBetween('tanggal', [$this->tgl_awal.' 00:00:00', $this->tgl_akhir.' 23:59:59'])
            )
            ->when($this->kasir_id, fn($q) => $q->where('kasir_id', $this->kasir_id))
            ->when($this->metode !== '', fn($q) => $q->where('metode_bayar', $this->metode))
            ->when($s !== '', fn($q) => $q->where('nomor','like',"%{$s}%"));

        $rows = (clone $base)
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $sumFiltered   = (clone $base)->sum('total');
        $countFiltered = (clone $base)->count();
        $sumPage       = collect($rows->items())->sum('total');

        return view('livewire.sales.penjualan-list', compact('rows','sumFiltered','countFiltered','sumPage'));
    }
}
