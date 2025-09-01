<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSatuanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('satuan')->insert([
            ['nama' => 'Pcs', 'deskripsi' => 'Satuan per item'],
            ['nama' => 'Pack', 'deskripsi' => 'Satuan per bungkus'],
        ]);
    }
}
