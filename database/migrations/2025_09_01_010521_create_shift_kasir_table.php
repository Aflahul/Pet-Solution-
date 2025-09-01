<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shift_kasir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('pengguna')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->integer('saldo_awal')->default(0);
            $table->integer('saldo_akhir')->default(0);
            $table->timestamps();

            $table->index(['kasir_id','waktu_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_kasir');
    }
};
