<?php
namespace App\Support;

use Illuminate\Support\Facades\DB;

class Nota
{
    public static function generate(string $prefix = 'POS'): string
    {
        $today = now()->format('Ymd');
        $base  = "{$prefix}-{$today}-";

        $last = DB::table('penjualan')
            ->whereDate('tanggal', now()->toDateString())
            ->where('nomor', 'like', $base.'%')
            ->orderBy('nomor','desc')
            ->value('nomor');

        $seq = 1;
        if ($last) {
            $seq = intval(substr($last, -4)) + 1;
        }
        return $base . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
