<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyPelangganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            [
                'nama'       => 'Andi',
                'telepon'    => '08123456789',
                'alamat'     => 'Jl. Merpati No.1',
                'tipe'       => 'Umum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Siti',
                'telepon'    => '082233445566',
                'alamat'     => 'Jl. Kenari No.2',
                'tipe'       => 'Member',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
