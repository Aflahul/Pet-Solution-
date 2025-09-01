<?php

namespace Tests\Feature;

use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Produk;
use App\Models\StokMutasi;
use App\Support\Nota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PenjualanFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function bisa_menyimpan_penjualan_dan_update_stok_mutasi()
    {
        // seed data dasar
        $this->seed();

        // pastikan ada produk id 1 & 2
        $this->assertTrue(Produk::count() >= 1);

        // tambah stok awal via mutasi masuk
        StokMutasi::create(['produk_id'=>1,'jenis'=>'masuk','qty'=>50,'harga'=>2000,'referensi'=>'INIT','waktu'=>now()]);

        $nomor = Nota::generate();
        $now   = now();

        $penjualan = Penjualan::create([
            'nomor'=>$nomor,'tanggal'=>$now,'pelanggan_id'=>null,'kasir_id'=>1,
            'subtotal'=>6000,'diskon'=>0,'pajak'=>0,'total'=>6000,
            'bayar_tunai'=>6000,'kembalian'=>0,'metode_bayar'=>'TUNAI'
        ]);

        PenjualanItem::create(['penjualan_id'=>$penjualan->id,'produk_id'=>1,'qty'=>2,'harga'=>3000,'diskon'=>0]);

        // mutasi keluar
        StokMutasi::create([
            'produk_id'=>1,'jenis'=>'keluar','qty'=>2,'harga'=>3000,'referensi'=>$penjualan->nomor,'waktu'=>$now
        ]);

        // stok akhir = 48
        $stok = \DB::table('stok_mutasi')->where('produk_id',1)
            ->selectRaw("SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END) AS s")
            ->value('s');
        $this->assertEquals(48, (int)$stok);
    }
}
