<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pembelian', function (Blueprint $t) {
        $t->id();
        $t->string('nomor', 30)->unique();
        $t->timestamp('tanggal');
        $t->unsignedBigInteger('kasir_id')->nullable();
        $t->string('pemasok')->nullable();     // nama pemasok/supplier (opsional)
        $t->integer('subtotal')->default(0);
        $t->integer('diskon')->default(0);     // rupiah (dari %)
        $t->integer('pajak')->default(0);      // rupiah (dari %)
        $t->integer('total')->default(0);
        $t->integer('bayar_tunai')->default(0);
        $t->integer('kembalian')->default(0);
        $t->enum('metode_bayar', ['TUNAI','NON_TUNAI'])->default('TUNAI');
        $t->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
