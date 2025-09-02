<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    protected $table = 'pembelian_item';
    protected $fillable = ['pembelian_id','produk_id','qty','harga','diskon'];

    public function produk(){ return $this->belongsTo(Produk::class, 'produk_id'); }
    public function pembelian(){ return $this->belongsTo(Pembelian::class, 'pembelian_id'); }
}
