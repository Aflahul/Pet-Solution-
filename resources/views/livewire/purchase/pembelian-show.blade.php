<div class="bg-white p-4 rounded-xl shadow space-y-4">
  <div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold">Detail Pembelian</h2>
    <a href="{{ route('pembelian.list') }}" class="text-blue-600 hover:underline">← Kembali ke daftar</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
    <div>
      <div><span class="text-gray-500">Nomor:</span> <span class="font-mono">{{ $pembelian->nomor }}</span></div>
      <div><span class="text-gray-500">Tanggal:</span> {{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y H:i') }}</div>
      <div><span class="text-gray-500">Pemasok:</span> {{ $pembelian->pemasok->nama ?? $pembelian->pemasok ?? '-' }}</div>
      <div><span class="text-gray-500">Kasir ID:</span> {{ $pembelian->kasir_id }}</div>
      <div><span class="text-gray-500">Metode Bayar:</span> {{ $pembelian->metode_bayar }}</div>
    </div>
    <div>
      <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($pembelian->subtotal,0,',','.') }}</span></div>
      <div class="flex justify-between"><span>Diskon</span><span>{{ number_format($pembelian->diskon,0,',','.') }}</span></div>
      <div class="flex justify-between"><span>Pajak</span><span>{{ number_format($pembelian->pajak,0,',','.') }}</span></div>
      <div class="flex justify-between font-semibold"><span>Total</span><span>{{ number_format($pembelian->total,0,',','.') }}</span></div>
      <div class="flex justify-between"><span>Bayar Tunai</span><span>{{ number_format($pembelian->bayar_tunai,0,',','.') }}</span></div>
      <div class="flex justify-between"><span>Kembalian</span><span>{{ number_format($pembelian->kembalian,0,',','.') }}</span></div>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2">SKU</th>
          <th class="p-2">Nama</th>
          <th class="p-2 text-center">Qty</th>
          <th class="p-2 text-right">Harga</th>
          <th class="p-2 text-right">Diskon</th>
          <th class="p-2 text-right">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pembelian->items as $it)
          @php $sub = max(0, ($it->harga * $it->qty) - $it->diskon); @endphp
          <tr class="border-b">
            <td class="p-2 font-mono">{{ $it->produk->sku ?? '-' }}</td>
            <td class="p-2">{{ $it->produk->nama ?? '-' }}</td>
            <td class="p-2 text-center">{{ $it->qty }}</td>
            <td class="p-2 text-right">{{ number_format($it->harga,0,',','.') }}</td>
            <td class="p-2 text-right">{{ number_format($it->diskon,0,',','.') }}</td>
            <td class="p-2 text-right">{{ number_format($sub,0,',','.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
