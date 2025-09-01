<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->dateTime('tanggal');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('kasir_id')->constrained('pengguna')->cascadeOnUpdate()->restrictOnDelete();

            $table->integer('subtotal')->default(0);
            $table->integer('diskon')->default(0);
            $table->integer('pajak')->default(0);
            $table->integer('total')->default(0);

            $table->integer('bayar_tunai')->default(0);
            $table->integer('kembalian')->default(0);
            $table->string('metode_bayar', 30)->default('TUNAI');

            $table->timestamps();

            $table->index(['tanggal','kasir_id','pelanggan_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('penjualan'); }
};
