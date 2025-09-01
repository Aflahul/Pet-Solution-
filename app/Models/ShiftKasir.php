<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftKasir extends Model
{
    protected $table = 'shift_kasir';
    protected $fillable = ['kasir_id','waktu_mulai','waktu_selesai','saldo_awal','saldo_akhir'];
    protected $casts = ['waktu_mulai'=>'datetime','waktu_selesai'=>'datetime'];

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'kasir_id');
    }
}
