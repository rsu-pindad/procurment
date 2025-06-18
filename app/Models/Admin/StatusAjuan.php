<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StatusAjuan extends Model
{
    protected $fillable = [
        'nama_status_ajuan',
        'urutan_ajuan'
    ];
}
