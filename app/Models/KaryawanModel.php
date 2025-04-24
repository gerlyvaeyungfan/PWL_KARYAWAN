<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaryawanModel extends Model
{
    use HasFactory;

    protected $table = 'm_karyawan';
    protected $primaryKey = 'karyawan_id';

    protected $fillable = [
        'nama',
        'jabatan_id',
        'nama_jabatan',
        'alamat',
        'telepon',
        'email',
        
    ];

    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id', 'jabatan_id');
    }
}
