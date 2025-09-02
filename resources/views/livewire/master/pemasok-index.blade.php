<div class="bg-white p-4 rounded-xl shadow">
    @if (session('ok'))
      <div class="mb-3 rounded bg-green-50 px-3 py-2 text-green-700 text-sm">{{ session('ok') }}</div>
    @endif

    <div class="mb-4 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <input type="text" placeholder="Cari nama/kontak/alamat…" wire:model.live.debounce.400ms="cari"
                   class="w-72 rounded-lg border px-3 py-2" />
            <select wire:model="perPage" class="rounded-lg border px-2 py-2">
                <option value="10">10</option><option value="25">25</option><option value="50">50</option>
            </select>
        </div>
        <button wire:click="create"
                class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">+ Pemasok</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 cursor-pointer" wire:click="sortBy('nama')">
                        Nama @if($sortField==='nama') <span>({{ strtoupper($sortDir) }})</span> @endif
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('kontak')">
                        Kontak @if($sortField==='kontak') <span>({{ strtoupper($sortDir) }})</span> @endif
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('alamat')">
                        Alamat @if($sortField==='alamat') <span>({{ strtoupper($sortDir) }})</span> @endif
                    </th>
                    <th class="p-2 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    <tr class="border-b">
                        <td class="p-2">{{ $r->nama }}</td>
                        <td class="p-2">{{ $r->kontak }}</td>
                        <td class="p-2">{{ $r->alamat }}</td>
                        <td class="p-2">
                            <button wire:click="edit({{ $r->id }})"
                                    class="rounded bg-amber-500 px-3 py-1 text-white hover:bg-amber-600">Edit</button>
                            <button wire:click="confirmDelete({{ $r->id }})"
                                    class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>

    {{-- Modal Form --}}
    @if ($showForm)
      <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
        <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
          <h2 class="mb-3 text-lg font-semibold">{{ $editingId ? 'Edit' : 'Tambah' }} Pemasok</h2>

          <div class="mb-3">
            <label class="block text-sm font-medium">Nama</label>
            <input type="text" wire:model.live="nama" class="w-full rounded border px-3 py-2">
            @error('nama') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium">Kontak</label>
            <input type="text" wire:model.live="kontak" class="w-full rounded border px-3 py-2">
            @error('kontak') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium">Alamat</label>
            <textarea wire:model.live="alamat" class="w-full rounded border px-3 py-2"></textarea>
            @error('alamat') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="flex items-center justify-end gap-2">
            <button wire:click="$set('showForm', false)" class="rounded border px-3 py-2">Batal</button>
            <button wire:click="save" class="rounded bg-blue-600 px-4 py-2 text-white">Simpan</button>
          </div>
        </div>
      </div>
    @endif

    {{-- Modal Hapus --}}
    @if ($deleteId)
      <div class="fixed inset-0 bg-black/40 flex items-center justify-center">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
          <p class="mb-4">Hapus pemasok ini? Tindakan tidak bisa dibatalkan.</p>
          <div class="flex justify-end gap-2">
            <button wire:click="$set('deleteId', null)" class="rounded border px-3 py-2">Batal</button>
            <button wire:click="delete" class="rounded bg-red-600 px-4 py-2 text-white">Hapus</button>
          </div>
        </div>
      </div>
    @endif

    <script>
      document.addEventListener('keydown', (e) => {
        if (e.altKey && e.key.toLowerCase() === 'n') { @this.create(); }
        if (e.altKey && e.key.toLowerCase() === 's') { @this.save?.(); }
      });
    </script>
</div>
