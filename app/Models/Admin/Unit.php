<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'nama_unit',
        'keterangan_unit'
    ];
}
