<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JabatanModel extends Model
{
    use HasFactory;

    protected $table = 'm_jabatan';
    protected $primaryKey = 'jabatan_id';

    protected $fillable = [
        'jabatan_id',
        'nama_jabatan',
        'keterangan',
    ];

    public function karyawans()
    {
        return $this->hasMany(KaryawanModel::class, 'jabatan_id', 'jabatan_id');
    }
}
