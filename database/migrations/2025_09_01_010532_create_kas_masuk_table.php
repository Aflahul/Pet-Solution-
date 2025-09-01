<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kas_masuk', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('sumber', 120);
            $table->integer('jumlah');
            $table->string('keterangan', 200)->nullable();
            $table->timestamps();

            $table->index('tanggal');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kas_masuk');
    }
};
