<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
</div>
<div class="bg-white p-4 rounded-xl shadow space-y-4">
  @if (session('ok'))
    <div class="rounded bg-green-50 border border-green-200 px-3 py-2 text-green-700 text-sm">
      {{ session('ok') }}
    </div>
  @endif

  <div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold">Pemasok: {{ $pemasokNama }}</h2>
    <a href="{{ route('master.pemasok') }}" class="text-blue-600 hover:underline">← Kembali ke daftar pemasok</a>
  </div>

  {{-- Cari & Tambah Produk ke Mapping --}}
  <div class="rounded-lg border p-3">
    <label class="block text-sm font-medium mb-1">Tambah Produk ke pemasok ini</label>
    <input type="text" placeholder="Cari SKU / Nama / Barcode" wire:model.live.debounce.400ms="q"
           class="w-full rounded border px-3 py-2 mb-2">
    @if(!empty($suggest))
      <div class="rounded border bg-white max-h-64 overflow-y-auto">
        @foreach($suggest as $s)
          <div class="flex items-center justify-between px-3 py-2 border-b last:border-b-0">
            <div>
              <div class="font-medium">{{ $s['nama'] }}</div>
              <div class="text-xs text-gray-500">SKU: {{ $s['sku'] }} @if($s['barcode']) • Barcode: {{ $s['barcode'] }} @endif</div>
            </div>
            <button wire:click="addProduct({{ $s['id'] }})"
                    class="rounded bg-blue-600 px-3 py-1.5 text-white text-sm hover:bg-blue-700">Tambah</button>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Tabel Mapping --}}
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2">SKU</th>
          <th class="p-2">Nama</th>
          <th class="p-2">Supplier SKU</th>
          <th class="p-2">Supplier Barcode</th>
          <th class="p-2 text-right">Harga Terakhir</th>
          <th class="p-2 text-center">Lead Time (hari)</th>
          <th class="p-2 text-center">Default?</th>
          <th class="p-2 w-40">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i => $r)
          <tr class="border-b">
            <td class="p-2 font-mono">{{ $r['sku'] }}</td>
            <td class="p-2">{{ $r['nama'] }}</td>
            <td class="p-2">
              <input type="text" wire:model.live="rows.{{ $i }}.supplier_sku" class="w-40 rounded border px-2 py-1">
            </td>
            <td class="p-2">
              <input type="text" wire:model.live="rows.{{ $i }}.supplier_barcode" class="w-40 rounded border px-2 py-1">
            </td>
            <td class="p-2">
              <input type="number" min="0" step="1" wire:model.live="rows.{{ $i }}.harga_terakhir" class="w-32 rounded border px-2 py-1 text-right">
            </td>
            <td class="p-2 text-center">
              <input type="number" min="0" step="1" wire:model.live="rows.{{ $i }}.lead_time_hari" class="w-24 rounded border px-2 py-1 text-center">
            </td>
            <td class="p-2 text-center">
              <input type="checkbox" wire:model.live="rows.{{ $i }}.is_default">
            </td>
            <td class="p-2">
              <div class="flex gap-2">
                <button wire:click="saveRow({{ $i }})"
                        class="rounded bg-emerald-600 px-3 py-1 text-white hover:bg-emerald-700">Simpan</button>
                <button wire:click="deleteRow({{ $i }})"
                        class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Hapus</button>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="p-4 text-center text-gray-500">Belum ada mapping untuk pemasok ini.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
