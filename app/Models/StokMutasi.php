<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMutasi extends Model
{
    protected $table = 'stok_mutasi';
    protected $fillable = ['produk_id','jenis','qty','harga','referensi','waktu'];
    protected $casts = ['waktu'=>'datetime'];

    public function produk(): BelongsTo { return $this->belongsTo(Produk::class); }
}
