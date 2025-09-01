<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Support\Nota;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;


class NotaTest extends TestCase
{
    use RefreshDatabase;

  
    #[Test]
    public function generate_nomor_mengikuti_format_dan_increment() 

    {
        // Siapkan akun kasir minimal agar kasir_id=1 valid
        // (sesuaikan dengan skema tabelmu: 'pengguna' & 'peran')
        DB::table('peran')->insert([
            'nama' => 'Admin', 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $peranId = DB::table('peran')->where('nama','Admin')->value('id');

        DB::table('pengguna')->insert([
            'nama' => 'Administrator',
            'email' => 'admin@local.test',
            'kata_sandi_hash' => bcrypt('admin123'),
            'peran_id' => $peranId,
            'aktif' => 1,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);

        // 1) Generate nomor pertama
        $n1 = Nota::generate();
        $this->assertMatchesRegularExpression('/^POS-\d{8}-\d{4}$/', $n1);

        // 2) Simulasikan sudah ada 1 penjualan dengan nomor $n1 di hari yang sama
        DB::table('penjualan')->insert([
            'nomor'        => $n1,
            'tanggal'      => now(),
            'pelanggan_id' => null,
            'kasir_id'     => 1,         // sesuai akun admin yang baru dibuat
            'subtotal'     => 0,
            'diskon'       => 0,
            'pajak'        => 0,
            'total'        => 0,
            'bayar_tunai'  => 0,
            'kembalian'    => 0,
            'metode_bayar' => 'TUNAI',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 3) Generate lagi → harus increment
        $n2 = Nota::generate();
        $this->assertNotEquals($n1, $n2);
        $this->assertMatchesRegularExpression('/^POS-\d{8}-\d{4}$/', $n2);
    }
}
