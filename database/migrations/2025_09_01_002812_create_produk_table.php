<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('nama', 150);
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('satuan_id')->constrained('satuan')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('barcode', 100)->unique();
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->integer('stok_min')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('kategori_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
