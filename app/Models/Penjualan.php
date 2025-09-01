<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = [
        'nomor','tanggal','pelanggan_id','kasir_id',
        'subtotal','diskon','pajak','total',
        'bayar_tunai','kembalian','metode_bayar'
    ];
    protected $casts = ['tanggal'=>'datetime'];

    public function items(): HasMany { return $this->hasMany(PenjualanItem::class, 'penjualan_id'); }
    public function pelanggan(): BelongsTo { return $this->belongsTo(Pelanggan::class, 'pelanggan_id'); }
    public function kasir(): BelongsTo { return $this->belongsTo(Pengguna::class, 'kasir_id'); }
}
