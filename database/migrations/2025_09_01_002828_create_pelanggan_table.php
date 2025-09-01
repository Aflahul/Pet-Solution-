<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('telepon', 50)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->string('tipe', 50)->nullable(); // bisa "Member", "Umum"
            $table->timestamps();

            $table->index(['nama', 'telepon']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
