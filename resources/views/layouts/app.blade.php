<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'POS Turbo' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="mx-auto max-w-6xl p-4">
    <header class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Pet Solution</h1>
      <nav class="text-sm space-x-3">
        <a href="{{ route('kategori.index') }}" class="text-blue-600 hover:underline">Kategori</a>
        <a href="{{ route('satuan.index') }}" class="text-blue-600 hover:underline">Satuan</a>
        <a href="{{ route('produk.index') }}" class="text-blue-600 hover:underline">Produk</a>
        <a href="{{ route('kasir.index') }}" class="text-blue-600 hover:underline">Kasir</a>
        <a href="{{ route('master.pemasok') }}" class="text-blue-600 hover:underline">Pemasok</a>
        <a href="{{ route('pembelian.index') }}" class="text-blue-600 hover:underline">Restock</a>


      </nav>
    </header>
    {{ $slot ?? '' }}
  </div>
  @livewireScripts
</body>
</html>
