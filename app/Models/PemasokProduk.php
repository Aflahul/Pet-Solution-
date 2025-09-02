<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemasokProduk extends Model
{
    protected $table = 'pemasok_produk';
    protected $fillable = ['pemasok_id','produk_id','supplier_sku','supplier_barcode','harga_terakhir','lead_time_hari','is_default'];

    public function pemasok(){ return $this->belongsTo(Pemasok::class); }
    public function produk(){ return $this->belongsTo(Produk::class); }
}
