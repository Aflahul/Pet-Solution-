<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $table = 'pemasok';
    protected $fillable = ['nama','kontak','alamat']; // sesuai tabel kamu

    public function products()
    {
        return $this->belongsToMany(Produk::class, 'pemasok_produk')
            ->withPivot(['supplier_sku','supplier_barcode','harga_terakhir','lead_time_hari','is_default'])
            ->withTimestamps();
    }
}
