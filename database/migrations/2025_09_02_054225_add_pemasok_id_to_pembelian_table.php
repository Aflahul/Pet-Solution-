<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('pembelian', 'pemasok_id')) {
            Schema::table('pembelian', function (Blueprint $t) {
                $t->unsignedBigInteger('pemasok_id')->nullable()->after('kasir_id');

                // FK opsional; aman untuk di-skip jika tidak pakai FK
                // $t->foreign('pemasok_id')->references('id')->on('pemasok')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembelian', 'pemasok_id')) {
            Schema::table('pembelian', function (Blueprint $t) {
                // kalau tadi buat FK, drop dulu
                // $t->dropForeign(['pemasok_id']);
                $t->dropColumn('pemasok_id');
            });
        }
    }
};
