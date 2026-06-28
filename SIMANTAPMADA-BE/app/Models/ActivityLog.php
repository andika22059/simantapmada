<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'modul',
        'aksi',
        'deskripsi',
        'referensi',
        'pelaku',
    ];

    /**
     * Helper terpusat untuk mencatat aktivitas.
     * Dipanggil dari controller mana pun cukup 1 baris:
     *
     *   ActivityLog::catat('Surat Masuk', 'Surat dari Kecamatan', $surat->no_surat, $request);
     *
     * @param string       $modul     Nama modul (mis. "Surat Masuk")
     * @param string       $deskripsi Ringkasan aktivitas
     * @param string|null  $referensi Nomor/kode terkait (opsional)
     * @param mixed        $request   Request (opsional, buat ambil nama user)
     */
    public static function catat($modul, $deskripsi, $referensi = null, $request = null)
    {
        $pelaku = 'Admin Desa';
        if ($request && $request->user()) {
            $pelaku = $request->user()->nama_lengkap ?? $request->user()->name ?? 'Admin Desa';
        }

        // Bungkus try-catch biar logging TIDAK PERNAH bikin proses utama gagal.
        // Kalau pencatatan log error, data inti (surat/aset/dll) tetap tersimpan.
        try {
            return self::create([
                'modul'     => $modul,
                'aksi'      => 'Input',
                'deskripsi' => $deskripsi,
                'referensi' => $referensi,
                'pelaku'    => $pelaku,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mencatat activity log: ' . $e->getMessage());
            return null;
        }
    }
}
