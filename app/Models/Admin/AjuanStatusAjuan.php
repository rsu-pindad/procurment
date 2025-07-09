<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AjuanStatusAjuan extends Model
{
    protected $table = 'ajuan_status_ajuan'; // Nama tabel pivot/custom

    protected $fillable = [
        'ajuan_id',
        'status_ajuan_id',
        'updated_by',
        'realisasi',
        'result_realisasi',
    ];

    public $timestamps = true; // agar created_at & updated_at otomatis terisi

    // Jika kamu mau, tambahkan relasi ke Ajuan dan StatusAjuan juga:
    public function ajuan()
    {
        return $this->belongsTo(\App\Models\Ajuan::class, 'ajuan_id');
    }

    public function status_ajuan()
    {
        return $this->belongsTo(\App\Models\Admin\StatusAjuan::class, 'status_ajuan_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
