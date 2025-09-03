<div class="bg-white p-4 rounded-xl shadow">
  <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
    <div class="flex flex-wrap items-end gap-3">
      <div>
        <label class="block text-xs text-gray-500 mb-1">Cari Nomor</label>
        <input type="text" wire:model.live.debounce.400ms="cari"
               class="w-56 rounded-lg border px-3 py-2" placeholder="cth: POS-2025...">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Kasir ID</label>
        <input type="number" min="1" wire:model.live="kasir_id" class="w-32 rounded-lg border px-3 py-2" placeholder="cth: 1">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Metode</label>
        <select wire:model.live="metode" class="w-40 rounded-lg border px-3 py-2">
          <option value="">— semua —</option>
          <option value="TUNAI">TUNAI</option>
          <option value="NON_TUNAI">NON_TUNAI</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Dari</label>
        <input type="date" wire:model.live="tgl_awal" class="rounded-lg border px-3 py-2">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
        <input type="date" wire:model.live="tgl_akhir" class="rounded-lg border px-3 py-2">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Per halaman</label>
        <select wire:model.live="perPage" class="rounded-lg border px-3 py-2">
          <option value="10">10</option><option value="25">25</option><option value="50">50</option>
        </select>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('kasir.index') ?? '/kasir' }}" class="rounded border px-3 py-2 text-sm">+ Transaksi Baru</a>
      <button wire:click="resetFilters" class="rounded border px-3 py-2 text-sm">Reset</button>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2 cursor-pointer" wire:click="sortBy('tanggal')">
            Tanggal @if($sortField==='tanggal') <span>({{ strtoupper($sortDir) }})</span> @endif
          </th>
          <th class="p-2">Nomor</th>
          <th class="p-2 text-center">Kasir</th>
          <th class="p-2 text-right">Subtotal</th>
          <th class="p-2 text-right">Diskon</th>
          <th class="p-2 text-right">Pajak</th>
          <th class="p-2 text-right cursor-pointer" wire:click="sortBy('total')">
            Total @if($sortField==='total') <span>({{ strtoupper($sortDir) }})</span> @endif
          </th>
          <th class="p-2">Metode</th>
          <th class="p-2 w-32"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr class="border-b">
            <td class="p-2">{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y H:i') }}</td>
            <td class="p-2 font-mono">{{ $r->nomor }}</td>
            <td class="p-2 text-center">{{ $r->kasir_id }}</td>
            <td class="p-2 text-right">{{ number_format($r->subtotal,0,',','.') }}</td>
            <td class="p-2 text-right">{{ number_format($r->diskon,0,',','.') }}</td>
            <td class="p-2 text-right">{{ number_format($r->pajak,0,',','.') }}</td>
            <td class="p-2 text-right font-semibold">{{ number_format($r->total,0,',','.') }}</td>
            <td class="p-2">{{ $r->metode_bayar }}</td>
            <td class="p-2 flex gap-2">
              <a href="{{ route('penjualan.show', $r->id) }}"
                 class="rounded bg-sky-600 px-3 py-1 text-white hover:bg-sky-700">Detail</a>
              {{-- Link nota cetak (sesuaikan route-mu; fallback ke path standar) --}}
              <a href="{{ url('/kasir/nota/'.$r->id) }}"
                 class="rounded bg-emerald-600 px-3 py-1 text-white hover:bg-emerald-700">Nota</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="p-4 text-center text-gray-500">Tidak ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
    <div class="text-sm text-gray-600">
      Ditampilkan: <b>{{ $rows->firstItem() }}</b>–<b>{{ $rows->lastItem() }}</b> dari <b>{{ $rows->total() }}</b> trx.
      • Total halaman ini: <b>{{ number_format($sumPage,0,',','.') }}</b>
      • Total terfilter: <b>{{ number_format($sumFiltered,0,',','.') }}</b> ({{ $countFiltered }} trx)
    </div>
    <div>{{ $rows->links() }}</div>
  </div>
</div>
