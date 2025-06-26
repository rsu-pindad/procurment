<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StatusAjuan extends Model
{
    protected $fillable = [
        'nama_status_ajuan',
        'urutan_ajuan'
    ];

    public function ajuans()
    {
        return $this->belongsToMany(Ajuan::class, 'ajuan_status_ajuan', 'status_ajuan_id', 'ajuan_id')
            ->withPivot(['updated_by', 'realisasi', 'result_realisasi', 'created_at'])
            ->withTimestamps();
    }
}
