<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori')->insert([
            ['nama' => 'Makanan', 'deskripsi' => 'Produk makanan'],
            ['nama' => 'Minuman', 'deskripsi' => 'Produk minuman'],
        ]);
    }
}
