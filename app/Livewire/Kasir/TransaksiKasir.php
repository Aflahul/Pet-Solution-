<?php

namespace App\Livewire\Kasir;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\Nota;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\StokMutasi;

#[Layout('layouts.app', ['title' => 'Kasir'])]
class TransaksiKasir extends Component
{
    // Scan & keranjang
    public string $scan = '';
    /** @var array<int,array{produk_id:int, sku:string, nama:string, harga:int, qty:int, diskon:int}> */
    public array $cart = [];

    // Ringkasan
    public int $subtotal = 0;
    public bool $use_diskon = true;
    public bool $use_pajak  = true;

    public int $diskon_pct = 0;
    public int $pajak_pct  = 0;
    public int $diskon_rp = 0;
    public int $pajak_rp  = 0;
    public int $total = 0;

    // Pembayaran
    public string $metode_bayar = 'TUNAI'; // TUNAI | NON_TUNAI
    public int $bayar_tunai = 0;
    public int $kembalian = 0;

    // Lain
    public ?int $pelanggan_id = null;
    public int $kasir_id = 1; // gunakan auth id jika ada

    // UI
    public bool $showAlert = false;
    public string $alertMsg = '';

    public function mount(): void
{
    $this->kasir_id = auth()->id() ?? 1;
    $this->pajak_pct = 11;     // default PPN
    $this->use_diskon = true;  // diskon aktif awal
    $this->use_pajak  = true;  // pajak aktif awal
}
public function updatedUseDiskon(): void { $this->hitungUlang(); }
public function updatedUsePajak(): void  { $this->hitungUlang(); }

public function toggleDiskon(): void
{
    $this->use_diskon = !$this->use_diskon;
    $this->hitungUlang();
}
public function togglePajak(): void
{
    $this->use_pajak = !$this->use_pajak;
    $this->hitungUlang();
}

public function quickDiskon(int $pct): void
{
    $this->use_diskon = true;
    $this->diskon_pct = max(0, min(100, $pct));
    $this->hitungUlang();
}
public function quickPajak(int $pct): void
{
    $this->use_pajak = true;
    $this->pajak_pct = max(0, min(100, $pct));
    $this->hitungUlang();
}


public function clearCart(): void
{
    $this->cart = [];
    $this->hitungUlang();
}

public function toggleMetode(): void
{
    $this->metode_bayar = $this->metode_bayar === 'TUNAI' ? 'NON_TUNAI' : 'TUNAI';
    $this->hitungUlang();
}


    public function updatedScan(): void
    {
        // boleh dibiarkan kosong; scanner biasanya memicu enter
    }

    public function scanEnter(): void
    {
        $kode = trim($this->scan);
        if ($kode === '') return;

        $produk = Produk::query()
            ->select('id','sku','nama','harga_jual','barcode')
            ->where('sku',$kode)
            ->orWhere('barcode',$kode)
            ->first();

        if (!$produk) {
            $this->flashWarn("Produk dengan kode/barcode '{$kode}' tidak ditemukan.");
            $this->scan = '';
            return;
        }

        $this->tambahKeCart($produk->id, $produk->sku, $produk->nama, (int)$produk->harga_jual, 1);
        $this->scan = '';
        $this->hitungUlang();
    }

    public function tambahKeCart(int $produkId, string $sku, string $nama, int $harga, int $qty = 1): void
    {
        foreach ($this->cart as &$row) {
            if ($row['produk_id'] === $produkId) {
                $row['qty'] += $qty;
                $this->hitungUlang();
                return;
            }
        }
        $this->cart[] = [
            'produk_id' => $produkId,
            'sku'       => $sku,
            'nama'      => $nama,
            'harga'     => $harga,
            'qty'       => $qty,
            'diskon'    => 0,
        ];
        $this->hitungUlang();
    }

    public function setQty(int $index, int $qty): void
    {
        if (!isset($this->cart[$index])) return;
        $this->cart[$index]['qty'] = max(1, $qty);
        $this->hitungUlang();
    }

    public function setDiskonBaris(int $index, int $diskon): void
    {
        if (!isset($this->cart[$index])) return;
        $this->cart[$index]['diskon'] = max(0, $diskon);
        $this->hitungUlang();
    }

    public function hapusBaris(int $index): void
    {
        if (!isset($this->cart[$index])) return;
        array_splice($this->cart, $index, 1);
        $this->hitungUlang();
    }

    // Livewire v3: nama method updated + StudlyCase dari properti snake_case
    public function updatedDiskonPct(): void { $this->diskon_pct = max(0, min(100, (int)$this->diskon_pct)); $this->hitungUlang(); }
    public function updatedPajakPct(): void  { $this->pajak_pct  = max(0, min(100, (int)$this->pajak_pct));  $this->hitungUlang(); }
    public function updatedBayarTunai(): void { $this->bayar_tunai = max(0, (int)$this->bayar_tunai); $this->hitungUlang(); }
    public function updatedMetodeBayar(): void { $this->hitungUlang(); }

    private function hitungUlang(): void
{
    $sub = 0;
    foreach ($this->cart as $row) {
        $line = ($row['harga'] * $row['qty']) - $row['diskon'];
        $sub += max(0, $line);
    }
    $this->subtotal = max(0, $sub);

    // ✅ hanya hitung jika aktif
    $this->diskon_rp = $this->use_diskon
        ? (int) round($this->subtotal * ($this->diskon_pct / 100), 0)
        : 0;

    $dasar_pajak = max(0, $this->subtotal - $this->diskon_rp);

    $this->pajak_rp = $this->use_pajak
        ? (int) round($dasar_pajak * ($this->pajak_pct / 100), 0)
        : 0;

    $this->total = max(0, $this->subtotal - $this->diskon_rp + $this->pajak_rp);

    if ($this->metode_bayar === 'TUNAI') {
        $this->kembalian = max(0, $this->bayar_tunai - $this->total);
    } else {
        $this->kembalian = 0;
    }
}


    // Tombol bayar cepat
    public function addBayar(int $nominal): void
    {
        $this->bayar_tunai = max(0, $this->bayar_tunai + $nominal);
        $this->hitungUlang();
    }
    public function resetBayar(): void
    {
        $this->bayar_tunai = 0;
        $this->hitungUlang();
    }
    public function setBayarTotal(): void
    {
        $this->bayar_tunai = $this->total;
        $this->hitungUlang();
    }

    private function cekStokCukup(): array
    {
        $kurang = [];
        foreach ($this->cart as $row) {
            $saldo = (int) DB::table('stok_mutasi')
                ->where('produk_id', $row['produk_id'])
                ->selectRaw("COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END),0) as s")
                ->value('s');
            if ($saldo < $row['qty']) {
                $kurang[] = "{$row['nama']} (butuh {$row['qty']}, stok {$saldo})";
            }
        }
        return $kurang;
    }

    public function simpan(): void
    {
        if (empty($this->cart)) {
            $this->flashWarn('Keranjang kosong.');
            return;
        }
        $this->hitungUlang();

        if ($this->metode_bayar === 'TUNAI' && $this->bayar_tunai < $this->total) {
            throw ValidationException::withMessages([
                'bayar_tunai' => 'Pembayaran tunai kurang dari total.',
            ]);
        }

        if ($kurang = $this->cekStokCukup()) {
            $this->flashWarn('Stok kurang: '.implode(', ', $kurang));
            return;
        }

        $penjualanId = null;

        DB::transaction(function () use (&$penjualanId) {
            $nomor = Nota::generate();
            $now = now();

            $penjualan = Penjualan::create([
                'nomor'        => $nomor,
                'tanggal'      => $now,
                'pelanggan_id' => $this->pelanggan_id,
                'kasir_id'     => $this->kasir_id,
                'subtotal'     => $this->subtotal,
                'diskon'       => $this->diskon_rp,   // simpan rupiah
                'pajak'        => $this->pajak_rp,    // simpan rupiah
                'total'        => $this->total,
                'bayar_tunai'  => $this->metode_bayar === 'TUNAI' ? $this->bayar_tunai : 0,
                'kembalian'    => $this->kembalian,
                'metode_bayar' => $this->metode_bayar,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            foreach ($this->cart as $row) {
                PenjualanItem::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id'    => $row['produk_id'],
                    'qty'          => $row['qty'],
                    'harga'        => $row['harga'],
                    'diskon'       => $row['diskon'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                // stok keluar
                StokMutasi::create([
                    'produk_id' => $row['produk_id'],
                    'jenis'     => 'keluar',
                    'qty'       => $row['qty'],
                    'harga'     => $row['harga'],
                    'referensi' => $penjualan->nomor,
                    'waktu'     => $now,
                    'created_at'=> $now,
                    'updated_at'=> $now,
                ]);
            }

            $penjualanId = $penjualan->id;
        });

        // reset keranjang
        $this->cart = [];
        $this->subtotal = $this->diskon_rp = $this->pajak_rp = $this->total = 0;
        $this->diskon_pct = $this->pajak_pct = 0;
        $this->bayar_tunai = $this->kembalian = 0;
        $this->metode_bayar = 'TUNAI';

        session()->flash('ok', 'Transaksi tersimpan.');
        $this->redirectRoute('kasir.nota', ['penjualan' => $penjualanId], navigate: true);
    }

    private function flashWarn(string $msg): void
    {
        $this->showAlert = true;
        $this->alertMsg = $msg;
    }

    public function render()
    {
        return view('livewire.kasir.transaksi-kasir');
    }
    
}
