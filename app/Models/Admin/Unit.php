<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nama_unit',
        'keterangan_unit'
    ];

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function ajuans()
    {
        return $this->hasMany(\App\Models\Ajuan::class);
    }
}
