<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\Kategori;

#[Layout('layouts.app', ['title' => 'Master Kategori'])]
class KategoriIndex extends Component
{
    use WithPagination;

    public string $cari = '';
    public string $sortField = 'nama';
    public string $sortDir = 'asc';
    public int $perPage = 10;

    public ?int $editingId = null;
    public string $nama = '';
    public ?string $deskripsi = null;
    public bool $showForm = false;
    public ?int $deleteId = null;

    protected function rules(): array
    {
        return [
            'nama'      => ['required','min:2','max:80', Rule::unique('kategori','nama')->ignore($this->editingId)],
            'deskripsi' => ['nullable','max:200'],
        ];
    }

    public function updatingCari(){ $this->resetPage(); }
    public function sortBy(string $f){ $this->sortDir = $this->sortField===$f ? ($this->sortDir==='asc'?'desc':'asc') : 'asc'; $this->sortField=$f; $this->resetPage(); }

    public function create(){ $this->resetForm(); $this->showForm = true; }
    public function edit(int $id){ $r=Kategori::findOrFail($id); $this->editingId=$r->id; $this->nama=$r->nama; $this->deskripsi=$r->deskripsi; $this->showForm=true; }

    public function save(){
        $data = $this->validate();
        Kategori::updateOrCreate(['id'=>$this->editingId], $data);
        $this->resetForm();
        session()->flash('ok','Data kategori tersimpan.');
    }

    public function confirmDelete(int $id){ $this->deleteId=$id; }
    public function delete(){ if($this->deleteId){ Kategori::where('id',$this->deleteId)->delete(); $this->deleteId=null; session()->flash('ok','Data kategori terhapus.'); } }

    private function resetForm(){ $this->editingId=null; $this->nama=''; $this->deskripsi=null; $this->showForm=false; }

    public function render(){
        $s = trim($this->cari);

        $rows = Kategori::query()
            ->when($s !== '', function ($q) use ($s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('nama', 'like', "%{$s}%")
                    ->orWhere('deskripsi', 'like', "%{$s}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.master.kategori-index', compact('rows'));
    }
}
