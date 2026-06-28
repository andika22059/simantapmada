<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $fillable = [
        'no_surat',
        'pengirim',
        'perihal',
        'tanggal_surat',
        'file_surat',
        'tanggal_terima',
        'jenis_surat',
        'sifat',
        'disposisi_ke',
        'alamat',
        'keterangan',
        // Kolom disposisi
        'status_disposisi',
        'instruksi',
        'perlu_atensi_kades',
        'didisposisi_oleh',
        'tgl_disposisi',
        'atensi_kades',
        'tgl_atensi',
        'diteruskan_ke',
        'arahan_kades',
    ];

    protected $casts = [
        'perlu_atensi_kades' => 'boolean',
        'tgl_disposisi'      => 'datetime',
        'tgl_atensi'         => 'datetime',
    ];
}
