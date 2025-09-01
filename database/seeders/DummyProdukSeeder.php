<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyProdukSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produk')->insert([
            [
                'sku'         => 'PRD001',
                'nama'        => 'Indomie Goreng',
                'kategori_id' => 1, // pastikan kategori 'Makanan' ada
                'satuan_id'   => 1, // pastikan satuan 'Pcs' ada
                'barcode'     => '8991234567890',
                'harga_beli'  => 2500,
                'harga_jual'  => 3000,
                'stok_min'    => 10,
                'aktif'       => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'sku'         => 'PRD002',
                'nama'        => 'Aqua 600ml',
                'kategori_id' => 2, // pastikan kategori 'Minuman' ada
                'satuan_id'   => 1,
                'barcode'     => '8993214567890',
                'harga_beli'  => 2000,
                'harga_jual'  => 3000,
                'stok_min'    => 20,
                'aktif'       => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
