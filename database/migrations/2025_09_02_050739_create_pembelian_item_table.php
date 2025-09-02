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
    Schema::create('pembelian_item', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('pembelian_id');
        $t->unsignedBigInteger('produk_id');
        $t->integer('qty');
        $t->integer('harga');       // harga beli per item
        $t->integer('diskon')->default(0); // diskon per baris (rp)
        $t->timestamps();

        $t->index(['pembelian_id','produk_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_item');
    }
};
