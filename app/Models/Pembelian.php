<?php

namespace App\Models;

use App\Models\Pemasok;
use App\Models\PembelianItem;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $fillable = [
        'nomor','tanggal','kasir_id','pemasok',
        'subtotal','diskon','pajak','total',
        'bayar_tunai','kembalian','metode_bayar'
    ];

    public function items(){ return $this->hasMany(PembelianItem::class, 'pembelian_id'); }
    public function pemasok(){ return $this->belongsTo(\App\Models\Pemasok::class,'pemasok_id'); }

}
