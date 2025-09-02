<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Nota {{ $penjualan->nomor }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css'])
  <style>
    @media print { @page { size: 80mm auto; margin: 5mm; } }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; }
  </style>
</head>
<body class="p-4 text-sm mono">
  <h1 class="text-center text-base font-bold mb-2">POS TURBO</h1>
  <div class="flex justify-between">
    <div>No: {{ $penjualan->nomor }}</div>
    <div>{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}</div>
  </div>
  <hr class="my-2">
  <table class="w-full">
    @foreach($penjualan->items as $it)
      @php $sub = ($it->harga * $it->qty) - $it->diskon; @endphp
      <tr>
        <td>{{ $it->produk->nama ?? '-' }}</td>
        <td class="text-right">{{ number_format($it->qty,0,',','.') }} x {{ number_format($it->harga,0,',','.') }}</td>
      </tr>
      <tr>
        <td class="text-gray-500">SKU: {{ $it->produk->sku ?? '-' }}</td>
        <td class="text-right">Sub: {{ number_format($sub,0,',','.') }}</td>
      </tr>
    @endforeach
  </table>
  <hr class="my-2">
  <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($penjualan->subtotal,0,',','.') }}</span></div>
  <div class="flex justify-between"><span>Diskon</span><span>{{ number_format($penjualan->diskon,0,',','.') }}</span></div>
  <div class="flex justify-between"><span>Pajak</span><span>{{ number_format($penjualan->pajak,0,',','.') }}</span></div>
  <div class="flex justify-between font-bold text-base"><span>Total</span><span>{{ number_format($penjualan->total,0,',','.') }}</span></div>
  <div class="flex justify-between"><span>Bayar</span><span>{{ number_format($penjualan->bayar_tunai,0,',','.') }}</span></div>
  <div class="flex justify-between"><span>Kembali</span><span>{{ number_format($penjualan->kembalian,0,',','.') }}</span></div>
  <p class="text-center mt-2">Terima kasih 🙏</p>

  <script>window.print()</script>
</body>
</html>
