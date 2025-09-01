<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BootstrapPenggunaPeranSeeder extends Seeder
{
    public function run(): void
    {
        // Upsert peran
        $peranAdminId = DB::table('peran')->updateOrInsert(
            ['nama' => 'Admin'],
            ['nama' => 'Admin', 'updated_at' => now(), 'created_at' => now()]
        );

        $peranKasirId = DB::table('peran')->updateOrInsert(
            ['nama' => 'Kasir'],
            ['nama' => 'Kasir', 'updated_at' => now(), 'created_at' => now()]
        );

        // Ambil id peran admin
        $adminRoleId = DB::table('peran')->where('nama', 'Admin')->value('id');

        // Buat akun admin awal jika belum ada
        $email = 'admin@local.test';
        if (!DB::table('pengguna')->where('email', $email)->exists()) {
            DB::table('pengguna')->insert([
                'nama'            => 'Administrator',
                'email'           => $email,
                'kata_sandi_hash' => Hash::make('admin123'), // ganti setelah login pertama
                'peran_id'        => $adminRoleId,
                'aktif'           => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
