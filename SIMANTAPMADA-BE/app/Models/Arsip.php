<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $fillable = [
        'nomor_arsip',
        'pencipta',
        'judul',
        'kategori',
        'lokasi_fisik',
        'status',
        'durasi_retensi',
        'tgl_retensi',
        'nasib_akhir',
        'files',
    ];

    protected $casts = [
        'tgl_retensi' => 'date:Y-m-d',
        'files' => 'array',
    ];
}
