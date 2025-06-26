<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class KategoriPengajuan extends Model
{

    protected $fillable = [
        'nama_kategori',
        'deskripsi_kategori'
    ];

    public function ajuans()
    {
        return $this->belongsToMany(
            \App\Models\Ajuan::class,
            'ajuan_kategori_pengajuan',
            'kategori_pengajuan_id',
            'ajuan_id'
        );
    }
}
