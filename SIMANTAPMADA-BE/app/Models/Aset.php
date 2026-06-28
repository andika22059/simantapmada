<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $fillable = [
        'nomor_aset',
        'kodefikasi',
        'nup',
        'nama_aset',
        'jenis_aset',
        'merk_type',
        'bukti_kepemilikan',
        'tahun_perolehan',
        'ukuran',
        'kondisi',
        'lokasi',
        'nilai_perolehan',
        'sumber_dana',
        'penanggung_jawab',
        'keterangan',
        'detail',
        'foto',
    ];

    protected $casts = [
        'nilai_perolehan' => 'decimal:2',
        'ukuran'          => 'decimal:2',
        'detail'          => 'array', // 🔥 otomatis JSON <-> array
    ];

    // Simpan array foto sebagai JSON di kolom 'foto'
    public function setFotoAttribute($value)
    {
        $this->attributes['foto'] = is_array($value) ? json_encode($value) : $value;
    }

    // Setiap kali foto diakses, otomatis diubah dari path relatif jadi URL lengkap
    public function getFotoAttribute($value)
    {
        $paths = $value ? json_decode($value, true) : [];
        return array_map(fn($path) => url('storage/' . $path), $paths ?: []);
    }
}
