<div x-data="pembelianUI($wire)" @keydown.f2.prevent="$refs.scan.focus()" @keydown.f8.prevent="$wire.setBayarTotal()"
    @keydown.f6.prevent="$wire.toggleMetode()" class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    @if ($showAlert)
        <div class="lg:col-span-3 rounded bg-amber-50 border px-3 py-2 text-amber-800">{{ $alertMsg }}</div>
    @endif
    @if (session('ok'))
        <div class="lg:col-span-3 rounded bg-green-50 border px-3 py-2 text-green-700">{{ session('ok') }}</div>
    @endif

    {{-- KIRI --}}
    <div class="lg:col-span-2 bg-white p-4 rounded-xl shadow">
        <div class="flex items-center gap-2 mb-3">
            <input x-ref="scan" type="text" placeholder="Scan/ketik SKU atau Barcode lalu Enter"
                wire:model.live="scan" wire:keydown.enter.prevent="scanEnter"
                class="w-full rounded-lg border px-3 py-2 font-mono" />
            <button class="rounded border px-3 py-2 text-sm" @click="$refs.scan.focus()">F2 Fokus</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">SKU</th>
                        <th class="p-2">Nama</th>
                        <th class="p-2 text-right">Harga Beli</th>
                        <th class="p-2 text-center">Qty</th>
                        <th class="p-2 text-right">Diskon</th>
                        <th class="p-2 text-right">Subtotal</th>
                        <th class="p-2 w-16"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $i => $r)
                        @php $sub = max(0, ($r['harga'] * $r['qty']) - $r['diskon']); @endphp
                        <tr class="border-b">
                            <td class="p-2 font-mono">{{ $r['sku'] }}</td>
                            <td class="p-2">{{ $r['nama'] }}</td>
                            <td class="p-2">
                                <input type="number" min="0" step="1" value="{{ $r['harga'] }}"
                                    wire:change="setHarga({{ $i }}, $event.target.value)"
                                    class="w-28 rounded border px-2 py-1 text-right">
                            </td>
                            <td class="p-2">
                                <input type="number" min="1" step="1" value="{{ $r['qty'] }}"
                                    wire:change="setQty({{ $i }}, $event.target.value)"
                                    class="w-20 rounded border px-2 py-1 text-right">
                            </td>
                            <td class="p-2">
                                <input type="number" min="0" step="1" value="{{ $r['diskon'] }}"
                                    wire:change="setDiskonBaris({{ $i }}, $event.target.value)"
                                    class="w-24 rounded border px-2 py-1 text-right">
                            </td>
                            <td class="p-2 text-right">{{ number_format($sub, 0, ',', '.') }}</td>
                            <td class="p-2 text-right"><button wire:click="hapus({{ $i }})"
                                    class="rounded bg-red-600 px-2 py-1 text-white">X</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">Keranjang kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <label class="text-sm">Pemasok</label>
                <select wire:model.live="pemasok_id" class="rounded border px-2 py-2">
                    <option value="">-- pilih pemasok --</option>
                    @foreach ($pemasokList as $p)
                        <option value="{{ $p['id'] }}">{{ $p['nama'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="applySupplierPrices" @disabled(!$pemasok_id)
                    class="rounded border px-3 py-2 text-sm {{ $pemasok_id ? '' : 'opacity-50 cursor-not-allowed' }}">
                    Terapkan harga pemasok ke semua baris
                </button>

                <button wire:click="clearCart" class="rounded border px-3 py-2 text-sm">
                    Kosongkan Keranjang
                </button>
            </div>
        </div>

    </div>

    {{-- KANAN --}}
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
                    <input type="checkbox" wire:model.live="use_diskon"> Aktif
                </label>
            </div>
            <input type="number" min="0" max="100" step="1" wire:model.live="diskon_pct"
                class="w-24 rounded border px-2 py-1 text-right {{ $use_diskon ? '' : 'bg-gray-100 text-gray-400' }}"
                @disabled(!$use_diskon)>
            <span
                class="w-32 text-right {{ $use_diskon ? '' : 'line-through text-gray-400' }}">{{ number_format($diskon_rp, 0, ',', '.') }}</span>
        </div>
        <div class="flex flex-wrap gap-1 text-xs">
            @foreach ([0, 5, 10, 15, 20] as $d)
                <button type="button" wire:click="quickDiskon({{ $d }})"
                    class="rounded border px-2 py-1 {{ $use_diskon ? '' : 'opacity-50 cursor-not-allowed' }}"
                    @disabled(!$use_diskon)>{{ $d }}%</button>
            @endforeach
        </div>

        {{-- Pajak --}}
        <div class="flex items-center justify-between gap-2 pt-2">
            <div class="flex items-center gap-2 flex-1">
                <label class="font-medium">Pajak</label>
                <label class="text-xs flex items-center gap-1">
                    <input type="checkbox" wire:model.live="use_pajak"> Aktif
                </label>
            </div>
            <input type="number" min="0" max="100" step="1" wire:model.live="pajak_pct"
                class="w-24 rounded border px-2 py-1 text-right {{ $use_pajak ? '' : 'bg-gray-100 text-gray-400' }}"
                @disabled(!$use_pajak)>
            <span
                class="w-32 text-right {{ $use_pajak ? '' : 'line-through text-gray-400' }}">{{ number_format($pajak_rp, 0, ',', '.') }}</span>
        </div>
        <div class="flex flex-wrap gap-1 text-xs">
            @foreach ([0, 5, 10, 11, 12] as $p)
                <button type="button" wire:click="quickPajak({{ $p }})"
                    class="rounded border px-2 py-1 {{ $use_pajak ? '' : 'opacity-50 cursor-not-allowed' }}"
                    @disabled(!$use_pajak)>{{ $p }}%</button>
            @endforeach
        </div>

        <div class="flex items-center justify-between border-t pt-2">
            <span>Total</span>
            <span class="text-xl font-bold">{{ number_format($total, 0, ',', '.') }}</span>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium">Metode Bayar</label>
                <button type="button" wire:click="toggleMetode()" class="text-xs underline">Toggle (F6)</button>
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
                    <input type="text" x-model="bayarFormatted" @input="syncBayar" inputmode="numeric"
                        class="w-32 rounded border px-2 py-1 text-right">
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ([500, 1000, 2000, 5000, 10000, 20000, 50000, 100000] as $n)
                        <button type="button" wire:click="addBayar({{ $n }})"
                            class="rounded border px-2 py-1 text-sm">+{{ number_format($n, 0, ',', '.') }}</button>
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
                class="w-full rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Simpan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pembelianUI', ($wire) => ({
            bayarFormatted: '',
            fmt(n) {
                return (n || 0).toLocaleString('id-ID');
            },
            unfmt(s) {
                return parseInt(String(s).replace(/[^\d]/g, '')) || 0;
            },
            syncBayar() {
                const n = this.unfmt(this.bayarFormatted);
                this.bayarFormatted = this.fmt(n);
                $wire.set('bayar_tunai', n);
            },
            init() {
                this.bayarFormatted = this.fmt(@js($bayar_tunai ?? 0));
            }
        }))
    })
</script>
