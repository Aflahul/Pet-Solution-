<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('jenis', ['masuk','keluar']);
            $table->integer('qty');
            $table->integer('harga')->default(0);  // nilai per unit (opsional)
            $table->string('referensi', 40)->nullable(); // nomor dokumen/nota
            $table->dateTime('waktu');
            $table->timestamps();

            $table->index(['produk_id','waktu']);
        });
    }
    public function down(): void { Schema::dropIfExists('stok_mutasi'); }
};
