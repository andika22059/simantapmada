<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengaturan instansi (kop surat, nama & TTD Kepala Desa).
 * Disimpan sebagai file JSON di storage/app/pengaturan.json — TANPA migrasi DB,
 * jadi ganti Kades / ganti TTD cukup lewat halaman Pengaturan, tidak perlu
 * mengubah kode backend.
 */
class PengaturanController extends Controller
{
    private string $file = 'pengaturan.json';

    private function defaults(): array
    {
        return [
            'nama_kabupaten' => 'PEMERINTAH KABUPATEN NGANJUK',
            'nama_kecamatan' => 'KECAMATAN LOCERET',
            'nama_desa'      => 'DESA MACANAN',
            'alamat_desa'    => 'JL. PANGLIMA SUDIRMAN NO. 15 TELP. ___ KODE POS 64471',
            'email_desa'     => 'macanan117@gmail.com',
            'kode_pos'       => '64471',
            'kode_surat'     => '411.508.21',
            'nama_kades'     => 'MOHAMAT SOIB, S.Pd.',
            'jabatan_kades'  => 'KEPALA DESA MACANAN',
            'nip_kades'      => '',
            'ttd_kades'      => null,
            'logo_desa'      => null,
        ];
    }

    private function baca(): array
    {
        if (Storage::disk('local')->exists($this->file)) {
            $isi = json_decode(Storage::disk('local')->get($this->file), true);
            if (is_array($isi)) {
                return array_merge($this->defaults(), $isi);
            }
        }
        return $this->defaults();
    }

    public function show()
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->baca(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_kades'    => 'nullable|string|max:150',
            'jabatan_kades' => 'nullable|string|max:150',
            'nip_kades'     => 'nullable|string|max:50',
            'nama_kabupaten' => 'nullable|string|max:150',
            'nama_kecamatan' => 'nullable|string|max:150',
            'nama_desa'      => 'nullable|string|max:150',
            'alamat_desa'    => 'nullable|string|max:255',
            'email_desa'     => 'nullable|string|max:150',
            'kode_pos'       => 'nullable|string|max:20',
            'kode_surat'     => 'nullable|string|max:50',
            'ttd_kades'      => 'nullable|image|max:2048',
            'logo_desa'      => 'nullable|image|max:2048',
        ]);

        $data = $this->baca();

        foreach ([
            'nama_kades', 'jabatan_kades', 'nip_kades', 'nama_kabupaten',
            'nama_kecamatan', 'nama_desa', 'alamat_desa', 'email_desa',
            'kode_pos', 'kode_surat',
        ] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->hasFile('ttd_kades')) {
            if (!empty($data['ttd_kades'])) {
                Storage::disk('public')->delete($data['ttd_kades']);
            }
            $data['ttd_kades'] = $request->file('ttd_kades')->store('pengaturan', 'public');
        }
        if ($request->hasFile('logo_desa')) {
            if (!empty($data['logo_desa'])) {
                Storage::disk('public')->delete($data['logo_desa']);
            }
            $data['logo_desa'] = $request->file('logo_desa')->store('pengaturan', 'public');
        }

        Storage::disk('local')->put($this->file, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengaturan berhasil disimpan.',
            'data'    => $data,
        ]);
    }
}
