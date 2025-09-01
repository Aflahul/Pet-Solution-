<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasMasuk extends Model
{
    protected $table = 'kas_masuk';
    protected $fillable = ['tanggal','sumber','jumlah','keterangan'];
    protected $casts = ['tanggal'=>'date'];
}
