<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    protected $fillable = ['nama','email','kata_sandi_hash','peran_id','aktif'];
    protected $hidden  = ['kata_sandi_hash'];
    protected $casts   = ['aktif' => 'boolean'];

    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'peran_id');
    }
}
