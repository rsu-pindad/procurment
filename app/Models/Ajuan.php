<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Ajuan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'units_id',
        'tanggal_ajuan',
        'hps',
        'spesifikasi',
        'file_rab',
        'file_nota_dinas',
        'file_analisa_kajian',
        'jenis_ajuan',
        'tanggal_update_terakhir',
        'status_ajuans_id',
        'users_id',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'users_id', 'id');
    }
}
