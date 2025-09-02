<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\Pemasok;

#[Layout('layouts.app', ['title' => 'Master Pemasok'])]
class PemasokIndex extends Component
{
    use WithPagination;

    // filter & sort
    public string $cari = '';
    public string $sortField = 'nama';
    public string $sortDir = 'asc';
    public int $perPage = 10;

    // form state
    public ?int $editingId = null;
    public string $nama = '';
    public ?string $kontak = null;
    public ?string $alamat = null;
    public bool $showForm = false;

    // delete confirm
    public ?int $deleteId = null;

    // validasi
    protected function rules(): array
    {
        return [
            'nama'   => ['required','min:2','max:150',
                Rule::unique('pemasok','nama')->ignore($this->editingId)],
            'kontak' => ['nullable','max:100'],
            'alamat' => ['nullable','max:200'],
        ];
    }

    protected array $validationAttributes = [
        'nama'   => 'Nama',
        'kontak' => 'Kontak',
        'alamat' => 'Alamat',
    ];

    // events
    public function updatingCari(){ $this->resetPage(); }

    public function sortBy(string $field): void
    {
        $this->sortDir = $this->sortField === $field
            ? ($this->sortDir === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    // CRUD
    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $r = Pemasok::findOrFail($id);
        $this->editingId = $r->id;
        $this->nama      = $r->nama ?? '';
        $this->kontak    = $r->kontak;
        $this->alamat    = $r->alamat;
        $this->showForm  = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        Pemasok::updateOrCreate(['id'=>$this->editingId], $data);

        $this->resetForm();
        session()->flash('ok','Data pemasok tersimpan.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Pemasok::where('id',$this->deleteId)->delete();
            $this->deleteId = null;
            session()->flash('ok','Data pemasok terhapus.');
        }
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->nama = '';
        $this->kontak = null;
        $this->alamat = null;
        $this->showForm = false;
    }

    public function render()
    {
        $s = trim($this->cari);

        $rows = Pemasok::query()
            ->when($s !== '', function($q) use ($s){
                $q->where(function($qq) use ($s){
                    $qq->where('nama','like',"%{$s}%")
                       ->orWhere('kontak','like',"%{$s}%")
                       ->orWhere('alamat','like',"%{$s}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.master.pemasok-index', compact('rows'));
    }
}
