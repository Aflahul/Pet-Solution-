<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

use App\Models\Pemasok;
use App\Models\Produk;
use App\Models\PemasokProduk;

#[Layout('layouts.app', ['title' => 'Mapping Produk per Pemasok'])]
class PemasokProdukMap extends Component
{
    use WithPagination;

    public int $pemasokId;
    public ?string $pemasokNama = null;

    // pencarian produk untuk ditambahkan
    public string $q = '';
    /** @var array<int,array{id:int,sku:string,nama:string,barcode:?string}> */
    public array $suggest = [];

    /**
     * Baris mapping yang ditampilkan dan diedit di tabel.
     * @var array<int,array{
     *   id:int, produk_id:int, sku:string, nama:string,
     *   supplier_sku:?string, supplier_barcode:?string,
     *   harga_terakhir:int, lead_time_hari:?int, is_default:bool
     * }>
     */
    public array $rows = [];

    public function mount(Pemasok $pemasok): void
    {
        $this->pemasokId = $pemasok->id;
        $this->pemasokNama = $pemasok->nama;
        $this->loadRows();
    }

    private function loadRows(): void
    {
        $maps = PemasokProduk::query()
            ->where('pemasok_id', $this->pemasokId)
            ->join('produk','produk.id','=','pemasok_produk.produk_id')
            ->orderBy('produk.nama')
            ->get([
                'pemasok_produk.id',
                'pemasok_produk.produk_id',
                'produk.sku', 'produk.nama',
                'pemasok_produk.supplier_sku',
                'pemasok_produk.supplier_barcode',
                'pemasok_produk.harga_terakhir',
                'pemasok_produk.lead_time_hari',
                'pemasok_produk.is_default',
            ]);

        $this->rows = $maps->map(function($m) {
            return [
                'id' => (int)$m->id,
                'produk_id' => (int)$m->produk_id,
                'sku' => (string)$m->sku,
                'nama' => (string)$m->nama,
                'supplier_sku' => $m->supplier_sku,
                'supplier_barcode' => $m->supplier_barcode,
                'harga_terakhir' => (int)$m->harga_terakhir,
                'lead_time_hari' => $m->lead_time_hari ? (int)$m->lead_time_hari : null,
                'is_default' => (bool)$m->is_default,
            ];
        })->all();
    }

    // Cari produk by SKU/Nama/Barcode (exclude yang sudah dimap)
    public function updatedQ(): void
    {
        $s = trim($this->q);
        if ($s === '') { $this->suggest = []; return; }

        $mappedIds = PemasokProduk::where('pemasok_id',$this->pemasokId)
            ->pluck('produk_id')->all();

        $this->suggest = Produk::query()
            ->when(!empty($mappedIds), fn($q)=>$q->whereNotIn('id',$mappedIds))
            ->where(function($q) use ($s) {
                $q->where('sku','like',"%{$s}%")
                  ->orWhere('nama','like',"%{$s}%")
                  ->orWhere('barcode','like',"%{$s}%");
            })
            ->orderBy('nama')
            ->limit(10)
            ->get(['id','sku','nama','barcode'])
            ->map(fn($p)=>[
                'id'=>$p->id,'sku'=>$p->sku,'nama'=>$p->nama,'barcode'=>$p->barcode
            ])->all();
    }

    public function addProduct(int $produkId): void
    {
        $p = Produk::findOrFail($produkId);

        // default: ambil harga_beli sebagai awal harga_terakhir
        $map = PemasokProduk::updateOrCreate(
            ['pemasok_id'=>$this->pemasokId,'produk_id'=>$p->id],
            ['harga_terakhir'=>(int)($p->harga_beli ?? 0)]
        );

        $this->q = '';
        $this->suggest = [];
        $this->loadRows();
        session()->flash('ok', "Produk {$p->nama} ditambahkan ke pemasok.");
    }

    public function saveRow(int $index): void
    {
        if (!isset($this->rows[$index])) return;
        $r = $this->rows[$index];

        PemasokProduk::where('id', $r['id'])->update([
            'supplier_sku'     => $r['supplier_sku'] ?: null,
            'supplier_barcode' => $r['supplier_barcode'] ?: null,
            'harga_terakhir'   => max(0, (int)$r['harga_terakhir']),
            'lead_time_hari'   => $r['lead_time_hari'] !== null ? max(0,(int)$r['lead_time_hari']) : null,
            'is_default'       => (bool)$r['is_default'],
        ]);

        session()->flash('ok', "Mapping {$r['nama']} tersimpan.");
        $this->loadRows();
    }

    public function deleteRow(int $index): void
    {
        if (!isset($this->rows[$index])) return;
        $r = $this->rows[$index];
        PemasokProduk::where('id', $r['id'])->delete();
        array_splice($this->rows, $index, 1);
        session()->flash('ok', "Mapping {$r['nama']} dihapus.");
    }

    public function render()
    {
        return view('livewire.master.pemasok-produk-map');
    }
}
