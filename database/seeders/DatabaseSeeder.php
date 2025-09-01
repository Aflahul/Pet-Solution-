<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Master\MasterSatuanSeeder;
use Database\Seeders\Master\MasterKategoriSeeder;
use Database\Seeders\BootstrapPenggunaPeranSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Master\MasterKategoriSeeder::class,
            \Database\Seeders\Master\MasterSatuanSeeder::class,
            \Database\Seeders\BootstrapPenggunaPeranSeeder::class,
        ]);
    }

}
