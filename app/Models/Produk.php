<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = [
        'sku','nama','kategori_id','satuan_id','barcode',
        'harga_beli','harga_jual','stok_min','aktif'
    ];
    protected $casts = ['aktif'=>'boolean'];

    public function kategori(): BelongsTo { return $this->belongsTo(Kategori::class); }
    public function satuan(): BelongsTo   { return $this->belongsTo(Satuan::class); }
}
