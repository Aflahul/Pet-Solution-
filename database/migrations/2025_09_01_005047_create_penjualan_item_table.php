<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();

            $table->integer('qty');
            $table->integer('harga');     // harga jual per item saat transaksi
            $table->integer('diskon')->default(0);

            $table->timestamps();

            $table->index(['penjualan_id','produk_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('penjualan_item'); }
};
