<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GajiModel extends Model
{
    use HasFactory;

    protected $table = 't_gaji';
    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'karyawan_id',
        'tanggal_transaksi',
        'gaji_pokok',
        'tunjangan',
        'potongan',
        'keterangan',
    ];

    // Supaya tidak null
    protected $attributes = [
        'tunjangan' => 0,
        'potongan' => 0,
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(KaryawanModel::class, 'karyawan_id', 'karyawan_id');
    }
}
