<div x-data="kasirUI($wire)" @keydown.f2.prevent="$refs.scan.focus()" @keydown.f4.prevent="$refs.diskon?.focus()"
    @keydown.f6.prevent="$wire.toggleMetode()" @keydown.f7.prevent="$wire.toggleDiskon()"
    @keydown.f5.prevent="$wire.togglePajak()" @keydown.f8.prevent="$wire.setBayarTotal()"
    @keydown.f9.prevent="$wire.simpan()" class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ALERT --}}
    @if ($showAlert)
        <div class="lg:col-span-3 rounded bg-amber-50 border border-amber-200 px-3 py-2 text-amber-800">
            {{ $alertMsg }}
        </div>
    @endif
    @if (session('ok'))
        <div class="lg:col-span-3 rounded bg-green-50 border border-green-200 px-3 py-2 text-green-700">
            {{ session('ok') }}
        </div>
    @endif

    {{-- KIRI: Scan + Keranjang --}}
    <div class="lg:col-span-2 bg-white p-4 rounded-xl shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2 w-full">
                <input x-ref="scan" type="text" placeholder="Scan SKU / Barcode (F2 fokus, Enter tambah)"
                    wire:model.live="scan" wire:keydown.enter.prevent="scanEnter"
                    class="w-full rounded-lg border px-3 py-2 font-mono" />
            </div>
            <div class="ml-3 flex items-center gap-2 text-xs text-gray-500">
                <span class="hidden md:inline">F2: Scan • F4: Diskon% • F6: Metode • F8: Pas • F9: Simpan</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">SKU</th>
                        <th class="p-2">Nama</th>
                        <th class="p-2 text-right">Harga</th>
                        <th class="p-2 text-center">Qty</th>
                        <th class="p-2 text-right">Diskon</th>
                        <th class="p-2 text-right">Subtotal</th>
                        <th class="p-2 w-16"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $i => $row)
                        @php $sub = max(0, ($row['harga'] * $row['qty']) - $row['diskon']); @endphp
                        <tr class="border-b">
                            <td class="p-2 font-mono">{{ $row['sku'] }}</td>
                            <td class="p-2">{{ $row['nama'] }}</td>
                            <td class="p-2 text-right">{{ number_format($row['harga'], 0, ',', '.') }}</td>
                            <td class="p-2">
                                <input type="number" min="1" step="1"
                                    wire:change="setQty({{ $i }}, $event.target.value)"
                                    value="{{ $row['qty'] }}" class="w-20 rounded border px-2 py-1 text-right">
                            </td>
                            <td class="p-2">
                                <input type="number" min="0" step="1"
                                    wire:change="setDiskonBaris({{ $i }}, $event.target.value)"
                                    value="{{ $row['diskon'] }}" class="w-24 rounded border px-2 py-1 text-right">
                            </td>
                            <td class="p-2 text-right">{{ number_format($sub, 0, ',', '.') }}</td>
                            <td class="p-2 text-right">
                                <button wire:click="hapusBaris({{ $i }})"
                                    class="rounded bg-red-600 px-2 py-1 text-white">X</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">Keranjang kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <button wire:click="clearCart" class="rounded border px-3 py-2 text-sm">Kosongkan Keranjang</button>
        </div>
    </div>

    {{-- KANAN: Ringkasan & Bayar --}}
    <div class="bg-white p-4 rounded-xl shadow space-y-3">
        <div class="flex items-center justify-between">
            <span>Subtotal</span>
            <span class="font-semibold">{{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>

        {{-- Diskon --}}
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 flex-1">
                <label class="font-medium">Diskon</label>
                <label class="text-xs flex items-center gap-1">
                    <input type="checkbox" wire:model.live="use_diskon">
                    Aktif
                </label>
            </div>
            <input x-ref="diskon" type="number" min="0" max="100" step="1"
                wire:model.live="diskon_pct" @disabled(!$use_diskon)
                class="w-24 rounded border px-2 py-1 text-right {{ $use_diskon ? '' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
            <span class="w-32 text-right {{ $use_diskon ? '' : 'line-through text-gray-400' }}">
                {{ number_format($diskon_rp, 0, ',', '.') }}
            </span>
        </div>
        <div class="flex flex-wrap gap-1 text-xs">
            @foreach ([0, 5, 10, 15, 20] as $d)
                <button type="button" wire:click="quickDiskon({{ $d }})" @disabled(!$use_diskon)
                    class="rounded border px-2 py-1 {{ $use_diskon ? '' : 'opacity-50 cursor-not-allowed' }}">
                    {{ $d }}%
                </button>
            @endforeach
        </div>
        {{-- Pajak --}}
        <div class="flex items-center justify-between gap-2 pt-2">
            <div class="flex items-center gap-2 flex-1">
                <label class="font-medium">Pajak</label>
                <label class="text-xs flex items-center gap-1">
                    <input type="checkbox" wire:model.live="use_pajak">
                    Aktif
                </label>
            </div>
            <input type="number" min="0" max="100" step="1" wire:model.live="pajak_pct"
                @disabled(!$use_pajak)
                class="w-24 rounded border px-2 py-1 text-right {{ $use_pajak ? '' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
            <span class="w-32 text-right {{ $use_pajak ? '' : 'line-through text-gray-400' }}">
                {{ number_format($pajak_rp, 0, ',', '.') }}
            </span>
        </div>
        <div class="flex flex-wrap gap-1 text-xs">
            @foreach ([0, 5, 10, 11, 12] as $p)
                <button type="button" wire:click="quickPajak({{ $p }})" @disabled(!$use_pajak)
                    class="rounded border px-2 py-1 {{ $use_pajak ? '' : 'opacity-50 cursor-not-allowed' }}">
                    {{ $p }}%
                </button>
            @endforeach
        </div>

        <div class="flex items-center justify-between border-t pt-2">
            <span>Total</span>
            <span class="text-xl font-bold">{{ number_format($total, 0, ',', '.') }}</span>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium">Metode Bayar</label>
                <button type="button" wire:click="toggleMetode()" class="text-xs underline">
                    Toggle (F6)
                </button>
            </div>
            <select wire:model.live="metode_bayar" class="w-full rounded border px-2 py-2">
                <option value="TUNAI">Tunai</option>
                <option value="NON_TUNAI">Non Tunai</option>
            </select>
        </div>

        @if ($metode_bayar === 'TUNAI')
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="flex-1">Bayar Tunai</label>
                    {{-- tampil rupiah, simpan integer --}}
                    <input type="text" x-model="bayarFormatted" @input="syncBayar" inputmode="numeric"
                        placeholder="0" class="w-32 rounded border px-2 py-1 text-right">
                </div>

                {{-- Tombol tambah cepat --}}
                <div class="flex flex-wrap gap-2">
                    @foreach ([500, 1000, 2000, 5000, 10000, 20000, 50000, 100000] as $n)
                        <button type="button" wire:click="addBayar({{ $n }})"
                            class="rounded border px-2 py-1 text-sm">
                            +{{ number_format($n, 0, ',', '.') }}
                        </button>
                    @endforeach
                    <button type="button" wire:click="setBayarTotal()"
                        class="rounded border px-2 py-1 text-sm">Pas</button>
                    <button type="button" wire:click="resetBayar()"
                        class="rounded border px-2 py-1 text-sm text-red-700 border-red-300">Reset</button>
                </div>

                <div class="flex items-center justify-between">
                    <span>Kembalian</span>
                    <span class="font-semibold">{{ number_format($kembalian, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <div class="pt-2">
            <button wire:click="simpan"
                class="w-full rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                Simpan (F9)
            </button>
        </div>
    </div>
</div>

{{-- Alpine helper untuk format Rupiah pada input Bayar --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kasirUI', ($wire) => ({
            bayarFormatted: '',
            format(n) {
                return (n || 0).toLocaleString('id-ID');
            },
            unformat(s) {
                return parseInt(String(s).replace(/[^\d]/g, '')) || 0;
            },
            syncBayar() {
                const n = this.unformat(this.bayarFormatted);
                this.bayarFormatted = this.format(n);
                $wire.set('bayar_tunai', n);
            },
            init() {
                // sinkron awal dari server ke tampilan
                this.bayarFormatted = this.format(@js($bayar_tunai ?? 0));
            }
        }))
    })
</script>
