<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_surat',
        'keterangan',
        'data_pengajuan',
        'lampiran',
        'status',
        'nomor_surat',
        'diverifikasi_oleh',
        'tgl_verifikasi',
        'disetujui_oleh',
        'tgl_persetujuan',
        'catatan',
    ];

    protected $casts = [
        'data_pengajuan'  => 'array',
        'lampiran'        => 'array',
        'tgl_verifikasi'  => 'datetime',
        'tgl_persetujuan' => 'datetime',
    ];
}
