<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->cascadeOnUpdate()->nullOnDelete();
            $table->string('aksi', 60);          // contoh: 'CREATE','UPDATE','DELETE','LOGIN'
            $table->string('entitas', 60);      // contoh: 'produk','penjualan'
            $table->unsignedBigInteger('entitas_id')->nullable();
            $table->dateTime('waktu');
            $table->timestamps();

            $table->index(['pengguna_id','waktu']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
