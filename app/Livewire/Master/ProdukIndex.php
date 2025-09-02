<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Satuan;

#[Layout('layouts.app', ['title' => 'Master Produk'])]
class ProdukIndex extends Component
{
    use WithPagination;

    // List & filter
    public string $cari = '';
    public string $sortField = 'nama';
    public string $sortDir = 'asc';
    public int $perPage = 10;

    public ?int $filterKategori = null;
    public ?int $filterSatuan = null;
    public ?string $filterAktif = ''; // '', '1', '0'

    // Form
    public ?int $editingId = null;
    public string $sku = '';
    public string $nama = '';
    public ?string $barcode = null;
    public ?int $kategori_id = null;
    public ?int $satuan_id = null;
    public int $harga_beli = 0;
    public int $harga_jual = 0;
    public int $stok_min = 0;
    public bool $aktif = true;

    public bool $showForm = false;
    public ?int $deleteId = null;

    // Dropdown options
    public array $kategoriList = [];
    public array $satuanList = [];

    protected function rules(): array
    {
        return [
            'sku'         => ['required','max:30', Rule::unique('produk','sku')->ignore($this->editingId)],
            'nama'        => ['required','min:2','max:100'],
            'barcode'     => ['nullable','max:32', Rule::unique('produk','barcode')->ignore($this->editingId)],
            'kategori_id' => ['required','integer','exists:kategori,id'],
            'satuan_id'   => ['required','integer','exists:satuan,id'],
            'harga_beli'  => ['required','integer','min:0'],
            'harga_jual'  => ['required','integer','min:0','gte:harga_beli'],
            'stok_min'    => ['required','integer','min:0'],
            'aktif'       => ['boolean'],
        ];
    }

    public function mount(): void
    {
        $this->kategoriList = Kategori::orderBy('nama')->get(['id','nama'])->toArray();
        $this->satuanList   = Satuan::orderBy('nama')->get(['id','nama'])->toArray();
    }

    public function updatingCari(){ $this->resetPage(); }
    public function updatedFilterKategori(){ $this->resetPage(); }
    public function updatedFilterSatuan(){ $this->resetPage(); }
    public function updatedFilterAktif(){ $this->resetPage(); }

    public function sortBy(string $f): void
    {
        $this->sortDir = $this->sortField === $f ? ($this->sortDir === 'asc' ? 'desc' : 'asc') : 'asc';
        $this->sortField = $f;
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $r = Produk::findOrFail($id);
        $this->editingId  = $r->id;
        $this->sku        = $r->sku;
        $this->nama       = $r->nama;
        $this->barcode    = $r->barcode;
        $this->kategori_id= $r->kategori_id;
        $this->satuan_id  = $r->satuan_id;
        $this->harga_beli = (int)$r->harga_beli;
        $this->harga_jual = (int)$r->harga_jual;
        $this->stok_min   = (int)$r->stok_min;
        $this->aktif      = (bool)$r->aktif;
        $this->showForm   = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        Produk::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->resetForm();
        session()->flash('ok','Data produk tersimpan.');
    }

    public function confirmDelete(int $id): void { $this->deleteId = $id; }

    public function delete(): void
    {
        if ($this->deleteId) {
            Produk::where('id',$this->deleteId)->delete();
            $this->deleteId = null;
            session()->flash('ok','Data produk terhapus.');
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->sku=''; $this->nama=''; $this->barcode=null;
        $this->kategori_id=null; $this->satuan_id=null;
        $this->harga_beli=0; $this->harga_jual=0; $this->stok_min=0;
        $this->aktif = true;
        $this->showForm = false;
    }

    public function render()
    {
        $s = trim($this->cari);

        $rows = Produk::query()
            ->with(['kategori:id,nama','satuan:id,nama'])
            ->when($s !== '', function($q) use ($s){
                $q->where(function($qq) use ($s){
                    $qq->where('nama','like',"%{$s}%")
                       ->orWhere('sku','like',"%{$s}%")
                       ->orWhere('barcode','like',"%{$s}%");
                });
            })
            ->when($this->filterKategori, fn($q)=> $q->where('kategori_id',$this->filterKategori))
            ->when($this->filterSatuan,   fn($q)=> $q->where('satuan_id',$this->filterSatuan))
            ->when($this->filterAktif !== '', fn($q)=> $q->where('aktif', (int)$this->filterAktif))
            ->orderBy($this->sortField,$this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.master.produk-index', [
            'rows' => $rows,
            'kategoriList' => $this->kategoriList,
            'satuanList' => $this->satuanList,
        ]);
    }
}
