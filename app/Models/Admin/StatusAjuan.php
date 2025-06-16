<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StatusAjuan extends Model
{
    protected $fillable = [
        'nama_ajuan',
        'urutan_ajuan'
    ];
}
