<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tujuan',
        'perihal',
        'tanggal_surat',
        'file_surat',
        'keterangan',
    ];


    protected $casts = [
        'tanggal_surat' => 'date:Y-m-d',
        'simpan_arsip' => 'boolean',
    ];

    // 🔥 Setiap kali file_surat diakses (termasuk saat dikirim sebagai JSON
    // ke frontend), otomatis diubah dari path relatif jadi URL lengkap.
    public function getFileSuratAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
