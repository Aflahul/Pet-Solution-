<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Pembelian;
use App\Models\Pemasok;

#[Layout('layouts.app', ['title' => 'Daftar Pembelian'])]
class PembelianList extends Component
{
    use WithPagination;

    // Filter & sort
    public string $cari = '';
    public ?int $pemasok_id = null;
    public string $tgl_awal;
    public string $tgl_akhir;
    public string $sortField = 'tanggal';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Dropdown pemasok
    public array $pemasokList = [];

    public function mount(): void
    {
        $this->tgl_akhir = now()->toDateString();
        $this->tgl_awal  = now()->subDays(30)->toDateString();
        $this->pemasokList = Pemasok::orderBy('nama')->get(['id','nama'])->toArray();
    }

    public function updatingCari(){ $this->resetPage(); }
    public function updatedPemasokId(){ $this->resetPage(); }
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
        $this->pemasok_id = null;
        $this->tgl_akhir = now()->toDateString();
        $this->tgl_awal  = now()->subDays(30)->toDateString();
        $this->resetPage();
    }

    public function render()
    {
        $s = trim($this->cari);

        $base = Pembelian::query()
            ->with('pemasok:id,nama')
            ->when($this->tgl_awal && $this->tgl_akhir, fn($q) =>
                $q->whereBetween('tanggal', [$this->tgl_awal.' 00:00:00', $this->tgl_akhir.' 23:59:59'])
            )
            ->when($this->pemasok_id, fn($q) => $q->where('pemasok_id', $this->pemasok_id))
            ->when($s !== '', function ($q) use ($s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('nomor', 'like', "%{$s}%")
                       ->orWhere('pemasok', 'like', "%{$s}%")
                       ->orWhereHas('pemasok', fn($qh) => $qh->where('nama', 'like', "%{$s}%"));
                });
            });

        $rows = (clone $base)
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $sumFiltered  = (clone $base)->sum('total');
        $countFiltered= (clone $base)->count();
        $sumPage      = collect($rows->items())->sum('total');

        return view('livewire.purchase.pembelian-list', compact('rows','sumFiltered','countFiltered','sumPage'));
    }
}
