<div class="bg-white p-4 rounded-xl shadow">
  @if (session('ok'))
    <div class="mb-3 rounded bg-green-50 px-3 py-2 text-green-700 text-sm">{{ session('ok') }}</div>
  @endif

  <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-3">
    <div class="flex items-center gap-2">
      <input type="text" placeholder="Cari nama/SKU/barcode…" wire:model.live.debounce.400ms="cari"
             class="w-64 rounded-lg border px-3 py-2" />
      <select wire:model.live="perPage" class="rounded-lg border px-2 py-2">
        <option value="10">10</option><option value="25">25</option><option value="50">50</option>
      </select>
    </div>
    <div class="flex items-center gap-2 justify-start md:justify-end">
      <select wire:model.live="filterKategori" class="rounded-lg border px-2 py-2">
        <option value="">-- Kategori --</option>
        @foreach($kategoriList as $k)
          <option value="{{ $k['id'] }}">{{ $k['nama'] }}</option>
        @endforeach
      </select>
      <select wire:model.live="filterSatuan" class="rounded-lg border px-2 py-2">
        <option value="">-- Satuan --</option>
        @foreach($satuanList as $s)
          <option value="{{ $s['id'] }}">{{ $s['nama'] }}</option>
        @endforeach
      </select>
      <select wire:model.live="filterAktif" class="rounded-lg border px-2 py-2">
        <option value="">Semua</option>
        <option value="1">Aktif</option>
        <option value="0">Nonaktif</option>
      </select>
      <button wire:click="create"
              class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        + Produk
      </button>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2 cursor-pointer" wire:click="sortBy('sku')">SKU @if($sortField==='sku') <span>({{ strtoupper($sortDir) }})</span>@endif</th>
          <th class="p-2 cursor-pointer" wire:click="sortBy('nama')">Nama @if($sortField==='nama') <span>({{ strtoupper($sortDir) }})</span>@endif</th>
          <th class="p-2">Kategori</th>
          <th class="p-2">Satuan</th>
          <th class="p-2 text-right cursor-pointer" wire:click="sortBy('harga_beli')">Harga Beli</th>
          <th class="p-2 text-right cursor-pointer" wire:click="sortBy('harga_jual')">Harga Jual</th>
          <th class="p-2 cursor-pointer" wire:click="sortBy('aktif')">Aktif</th>
          <th class="p-2 w-40">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr class="border-b">
            <td class="p-2 font-mono">{{ $r->sku }}</td>
            <td class="p-2">{{ $r->nama }}</td>
            <td class="p-2">{{ $r->kategori->nama ?? '-' }}</td>
            <td class="p-2">{{ $r->satuan->nama ?? '-' }}</td>
            <td class="p-2 text-right">{{ number_format($r->harga_beli,0,',','.') }}</td>
            <td class="p-2 text-right">{{ number_format($r->harga_jual,0,',','.') }}</td>
            <td class="p-2">
              @if($r->aktif)
                <span class="rounded bg-green-100 text-green-700 px-2 py-0.5 text-xs">Aktif</span>
              @else
                <span class="rounded bg-gray-200 text-gray-700 px-2 py-0.5 text-xs">Nonaktif</span>
              @endif
            </td>
            <td class="p-2">
              <button wire:click="edit({{ $r->id }})" class="rounded bg-amber-500 px-3 py-1 text-white hover:bg-amber-600">Edit</button>
              <button wire:click="confirmDelete({{ $r->id }})" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Hapus</button>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="p-4 text-center text-gray-500">Belum ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>

  {{-- Modal Form --}}
  @if($showForm)
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="w-full max-w-2xl rounded-xl bg-white p-5 shadow-xl">
      <h2 class="mb-3 text-lg font-semibold">{{ $editingId ? 'Edit' : 'Tambah' }} Produk</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">SKU</label>
          <input type="text" wire:model.live="sku" class="w-full rounded border px-3 py-2">
          @error('sku') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Barcode (opsional)</label>
          <input type="text" wire:model.live="barcode" class="w-full rounded border px-3 py-2">
          @error('barcode') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">Nama</label>
          <input type="text" wire:model.live="nama" class="w-full rounded border px-3 py-2">
          @error('nama') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Kategori</label>
          <select wire:model.live="kategori_id" class="w-full rounded border px-3 py-2">
            <option value="">-- pilih --</option>
            @foreach($kategoriList as $k)
              <option value="{{ $k['id'] }}">{{ $k['nama'] }}</option>
            @endforeach
          </select>
          @error('kategori_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Satuan</label>
          <select wire:model.live="satuan_id" class="w-full rounded border px-3 py-2">
            <option value="">-- pilih --</option>
            @foreach($satuanList as $s)
              <option value="{{ $s['id'] }}">{{ $s['nama'] }}</option>
            @endforeach
          </select>
          @error('satuan_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Harga Beli (Rp)</label>
          <input type="number" min="0" step="1" wire:model.live="harga_beli" class="w-full rounded border px-3 py-2 text-right">
          @error('harga_beli') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Harga Jual (Rp)</label>
          <input type="number" min="0" step="1" wire:model.live="harga_jual" class="w-full rounded border px-3 py-2 text-right">
          @error('harga_jual') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          @if($harga_jual !== null && $harga_beli !== null && $harga_jual < $harga_beli)
            <p class="text-xs text-amber-600 mt-1">Harga jual lebih kecil dari harga beli.</p>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium">Stok Minimum</label>
          <input type="number" min="0" step="1" wire:model.live="stok_min" class="w-full rounded border px-3 py-2 text-right">
          @error('stok_min') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-2 pt-6">
          <input id="aktif" type="checkbox" wire:model.live="aktif" class="h-4 w-4">
          <label for="aktif">Aktif</label>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-end gap-2">
        <button wire:click="$set('showForm', false)" class="rounded border px-3 py-2">Batal</button>
        <button wire:click="save" wire:loading.attr="disabled"
                class="rounded bg-blue-600 px-4 py-2 text-white">
          <span wire:loading.remove>Simpan</span>
          <span wire:loading>Menyimpan…</span>
        </button>
      </div>
    </div>
  </div>
  @endif

  {{-- Modal Konfirmasi Hapus --}}
  @if($deleteId)
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
      <p class="mb-4">Hapus produk ini? Tindakan tidak bisa dibatalkan.</p>
      <div class="flex justify-end gap-2">
        <button wire:click="$set('deleteId', null)" class="rounded border px-3 py-2">Batal</button>
        <button wire:click="delete" class="rounded bg-red-600 px-4 py-2 text-white">Hapus</button>
      </div>
    </div>
  </div>
  @endif
</div>
