<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyPemasokSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pemasok')->insert([
            [
                'nama'       => 'PT Indofood',
                'kontak'     => '021-555555',
                'alamat'     => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'PT Aqua Indonesia',
                'kontak'     => '021-666666',
                'alamat'     => 'Bekasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
