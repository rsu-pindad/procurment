<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReasonAjuan extends Model
{
    protected $fillable = [
        'ajuan_id',
        'status_ajuan_id',
        'created_by',
        'reason_keterangan_ajuan'
    ];

    public function ajuan()
    {
        return $this->belongsTo(\App\Models\Ajuan::class, 'ajuan_id', 'id');
    }

    public function status_ajuan()
    {
        return $this->belongsTo(\App\Models\Admin\StatusAjuan::class, 'status_ajuan_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
}
