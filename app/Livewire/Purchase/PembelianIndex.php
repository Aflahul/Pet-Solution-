<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Models\Produk;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\StokMutasi;
// 👇 tambahan
use App\Models\Pemasok;
use App\Models\PemasokProduk;

#[Layout('layouts.app', ['title' => 'Pembelian (Restock)'])]
class PembelianIndex extends Component
{
    public string $scan = '';
    /** @var array<int,array{produk_id:int, sku:string, nama:string, harga:int, qty:int, diskon:int}> */
    public array $cart = [];

    // ringkasan
    public int $subtotal = 0;

    // toggle & persen
    public bool $use_diskon = true;
    public bool $use_pajak  = true;
    public int  $diskon_pct = 0;
    public int  $pajak_pct  = 11;
    public int $diskon_rp = 0;
    public int $pajak_rp  = 0;
    public int $total = 0;

    // pembayaran
    public string $metode_bayar = 'TUNAI';
    public int $bayar_tunai = 0;
    public int $kembalian = 0;

    // info pemasok
    public ?int $pemasok_id = null;         // 👈 ID pemasok (baru)
    public array $pemasokList = [];         // 👈 untuk dropdown
    public ?string $pemasok = null;         // tetap dipakai jika kamu masih simpan nama pemasok di kolom string
    public int $kasir_id = 1;

    // alert
    public bool $showAlert = false;
    public string $alertMsg = '';

    public function mount(): void
    {
        $this->kasir_id = auth()->id() ?? 1;
        $this->pajak_pct = 11;

        // load daftar pemasok utk dropdown
        $this->pemasokList = Pemasok::orderBy('nama')->get(['id','nama'])->toArray();
    }

    // kalau ganti pemasok di dropdown, kamu boleh otomatis isi kolom nama pemasok (opsional)
    public function updatedPemasokId($id): void
    {
        $this->pemasok = $id ? (Pemasok::find($id)->nama ?? null) : null;
    }

    public function scanEnter(): void
    {
        $kode = trim($this->scan);
        if ($kode === '') return;

        $produk = Produk::query()
            ->select('id','sku','nama','harga_beli','barcode')
            ->where('sku',$kode)
            ->orWhere('barcode',$kode)
            ->first();

        if (!$produk) {
            $this->flashWarn("Produk '{$kode}' tidak ditemukan.");
            $this->scan = '';
            return;
        }

        // harga default: dari mapping pemasok–produk kalau ada; kalau tidak, pakai harga_beli produk
        $harga = (int)($produk->harga_beli ?? 0);
        if ($this->pemasok_id) {
            $map = PemasokProduk::where('pemasok_id', $this->pemasok_id)
                ->where('produk_id', $produk->id)
                ->first();
            if ($map && $map->harga_terakhir > 0) {
                $harga = (int)$map->harga_terakhir;
            }
        }

        $this->tambah($produk->id, $produk->sku, $produk->nama, $harga);
        $this->scan = '';
    }

    public function tambah(int $produkId, string $sku, string $nama, int $harga): void
    {
        foreach ($this->cart as &$row) {
            if ($row['produk_id'] === $produkId) {
                $row['qty'] += 1;
                $this->hitung();
                return;
            }
        }
        $this->cart[] = [
            'produk_id'=>$produkId,'sku'=>$sku,'nama'=>$nama,
            'harga'=>$harga,'qty'=>1,'diskon'=>0
        ];
        $this->hitung();
    }

    public function setQty(int $i, int $qty): void
    {
        if (!isset($this->cart[$i])) return;
        $this->cart[$i]['qty'] = max(1, (int)$qty);
        $this->hitung();
    }
    public function setHarga(int $i, int $harga): void
    {
        if (!isset($this->cart[$i])) return;
        $this->cart[$i]['harga'] = max(0, (int)$harga);
        $this->hitung();
    }
    public function setDiskonBaris(int $i, int $rp): void
    {
        if (!isset($this->cart[$i])) return;
        $this->cart[$i]['diskon'] = max(0, (int)$rp);
        $this->hitung();
    }
    public function hapus(int $i): void
    {
        array_splice($this->cart, $i, 1);
        $this->hitung();
    }
    public function clearCart(): void { $this->cart = []; $this->hitung(); }

    public function updatedUseDiskon(){ $this->hitung(); }
    public function updatedUsePajak(){ $this->hitung(); }
    public function quickDiskon(int $pct){ $this->use_diskon = true; $this->diskon_pct = max(0,min(100,$pct)); $this->hitung(); }
    public function quickPajak(int $pct){ $this->use_pajak  = true; $this->pajak_pct  = max(0,min(100,$pct)); $this->hitung(); }

    public function updatedBayarTunai(){ $this->bayar_tunai = max(0,(int)$this->bayar_tunai); $this->hitung(); }
    public function setBayarTotal(){ $this->bayar_tunai = $this->total; $this->hitung(); }
    public function addBayar(int $n){ $this->bayar_tunai = max(0,$this->bayar_tunai + $n); $this->hitung(); }
    public function resetBayar(){ $this->bayar_tunai = 0; $this->hitung(); }
    public function toggleMetode(){ $this->metode_bayar = $this->metode_bayar==='TUNAI'?'NON_TUNAI':'TUNAI'; $this->hitung(); }

    private function hitung(): void
    {
        $sub = 0;
        foreach ($this->cart as $r) {
            $line = ($r['harga'] * $r['qty']) - $r['diskon'];
            $sub += max(0, $line);
        }
        $this->subtotal = max(0,$sub);

        $this->diskon_rp = $this->use_diskon ? (int)round($this->subtotal * ($this->diskon_pct/100)) : 0;
        $dasar = max(0, $this->subtotal - $this->diskon_rp);
        $this->pajak_rp  = $this->use_pajak ? (int)round($dasar * ($this->pajak_pct/100)) : 0;

        $this->total = max(0, $this->subtotal - $this->diskon_rp + $this->pajak_rp);

        $this->kembalian = $this->metode_bayar==='TUNAI' ? max(0, $this->bayar_tunai - $this->total) : 0;
    }

    // Terapkan harga pemasok ke semua baris (opsional)
    public function applySupplierPrices(): void
    {
        if (!$this->pemasok_id) return;
        foreach ($this->cart as &$row) {
            $map = PemasokProduk::where('pemasok_id', $this->pemasok_id)
                ->where('produk_id', $row['produk_id'])
                ->first();
            if ($map && $map->harga_terakhir > 0) {
                $row['harga'] = (int)$map->harga_terakhir;
            }
        }
        $this->hitung();
    }

    public function simpan(): void
    {
        if (empty($this->cart)) { $this->flashWarn('Keranjang kosong.'); return; }
        $this->hitung();

        if ($this->metode_bayar==='TUNAI' && $this->bayar_tunai < $this->total) {
            throw ValidationException::withMessages(['bayar_tunai'=>'Pembayaran tunai kurang dari total.']);
        }

        DB::transaction(function(){
            $now = now();
            $nomor = $this->generateNomor();

            $pb = Pembelian::create([
                'nomor'        => $nomor,
                'tanggal'      => $now,
                'kasir_id'     => $this->kasir_id,
                'pemasok'      => $this->pemasok,      // tetap simpan nama kalau kamu butuh
                'pemasok_id'   => $this->pemasok_id,   // 👈 relasi pemasok (baru)
                'subtotal'     => $this->subtotal,
                'diskon'       => $this->diskon_rp,
                'pajak'        => $this->pajak_rp,
                'total'        => $this->total,
                'bayar_tunai'  => $this->metode_bayar==='TUNAI' ? $this->bayar_tunai : 0,
                'kembalian'    => $this->kembalian,
                'metode_bayar' => $this->metode_bayar,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            foreach ($this->cart as $r) {
                PembelianItem::create([
                    'pembelian_id'=>$pb->id,'produk_id'=>$r['produk_id'],
                    'qty'=>$r['qty'],'harga'=>$r['harga'],'diskon'=>$r['diskon'],
                    'created_at'=>$now,'updated_at'=>$now,
                ]);

                // stok masuk
                StokMutasi::create([
                    'produk_id'=>$r['produk_id'],'jenis'=>'masuk','qty'=>$r['qty'],
                    'harga'=>$r['harga'],'referensi'=>$pb->nomor,'waktu'=>$now,
                    'created_at'=>$now,'updated_at'=>$now,
                ]);

                // update harga_beli terakhir produk (opsional)
                DB::table('produk')->where('id',$r['produk_id'])->update(['harga_beli'=>$r['harga']]);

                // 👇 upsert mapping pemasok–produk (kalau pemasok dipilih)
                if ($this->pemasok_id) {
                    PemasokProduk::updateOrCreate(
                        ['pemasok_id'=>$this->pemasok_id, 'produk_id'=>$r['produk_id']],
                        ['harga_terakhir'=>$r['harga']]
                    );
                }
            }
        });

        // reset
        $this->cart=[];
        $this->subtotal=$this->diskon_rp=$this->pajak_rp=$this->total=0;
        $this->diskon_pct=0; $this->pajak_pct=11;
        $this->bayar_tunai=$this->kembalian=0;
        $this->metode_bayar='TUNAI';
        $this->pemasok_id=null; $this->pemasok=null;

        session()->flash('ok','Pembelian tersimpan & stok masuk.');
    }

    private function generateNomor(): string
    {
        $ymd = now()->format('Ymd');
        $count = (int) DB::table('pembelian')->whereDate('tanggal', now()->toDateString())->count() + 1;
        return 'PO-'.$ymd.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function flashWarn(string $msg): void { $this->showAlert=true; $this->alertMsg=$msg; }

    public function render(){ return view('livewire.purchase.pembelian-index'); }
}
