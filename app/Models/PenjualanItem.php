<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanItem extends Model
{
    protected $table = 'penjualan_item';
    protected $fillable = ['penjualan_id','produk_id','qty','harga','diskon'];

    public function penjualan(): BelongsTo { return $this->belongsTo(Penjualan::class); }
    public function produk(): BelongsTo { return $this->belongsTo(Produk::class); }
}
