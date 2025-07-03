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
        'produk_ajuan',
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin\Unit::class, 'units_id', 'id');
    }

    public function status_ajuan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin\StatusAjuan::class, 'status_ajuans_id', 'id');
    }

    public function kategori_pengajuans()
    {
        return $this->belongsToMany(
            \App\Models\Admin\KategoriPengajuan::class,
            'ajuan_kategori_pengajuan',
            'ajuan_id',
            'kategori_pengajuan_id'
        );
    }

    public function reason_pengajuans()
    {
        return $this->hasMany(
            \App\Models\Admin\ReasonAjuan::class,
            'ajuan_id',
            'id'
        );
    }


    public function statusHistories()
    {
        return $this->belongsToMany(\App\Models\Admin\StatusAjuan::class, 'ajuan_status_ajuan', 'ajuan_id', 'status_ajuan_id')
            ->withPivot(['updated_by', 'realisasi', 'result_realisasi', 'created_at'])
            ->withTimestamps();
    }

    public function addStatus($statusId, $userId = null)
    {
        $this->statusHistories()->attach($statusId, [
            'updated_by' => $userId,
            'created_at' => now(),
        ]);

        // Set juga status terakhir jika masih pakai kolom status_ajuans_id
        $this->status_ajuans_id = $statusId;
        $this->tanggal_update_terakhir = now();
        $this->save();
    }
}
