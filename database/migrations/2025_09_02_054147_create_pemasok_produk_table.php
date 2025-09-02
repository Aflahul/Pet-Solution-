<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pemasok_produk')) {
            Schema::create('pemasok_produk', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('pemasok_id');
                $t->unsignedBigInteger('produk_id');
                $t->string('supplier_sku', 60)->nullable();
                $t->string('supplier_barcode', 60)->nullable();
                $t->integer('harga_terakhir')->default(0);
                $t->smallInteger('lead_time_hari')->nullable();
                $t->boolean('is_default')->default(false);
                $t->timestamps();

                $t->unique(['pemasok_id','produk_id']);
                $t->index(['produk_id','pemasok_id']);

                // FK opsional; kalau kamu biasa tanpa FK, hapus 2 baris ini
                // $t->foreign('pemasok_id')->references('id')->on('pemasok')->cascadeOnDelete();
                // $t->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok_produk');
    }
};
