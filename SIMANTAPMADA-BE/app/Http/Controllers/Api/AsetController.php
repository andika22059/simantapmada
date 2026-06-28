<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aset; // <-- Manggil Model Aset biar bisa ngobrol sama database
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AsetController extends Controller
{
    // Fungsi buat nampilin semua data Aset (Read)
    public function index()
    {
        // Ngambil semua data dari tabel asets, diurutin dari yang paling baru
        $asets = Aset::orderBy('id', 'desc')->get();

        // Dihidangkan dalam bentuk JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data Aset berhasil diambil',
            'data' => $asets
        ], 200);
    }

    // Fungsi buat nyimpen Aset baru (Create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_aset'        => 'nullable|string|max:255|unique:asets,nomor_aset',
            'kodefikasi'        => 'nullable|string|max:255',
            'nup'               => 'nullable|string|max:255',
            'nama_aset'         => 'required|string|max:255',
            'jenis_aset'        => 'required|string|max:50',
            'merk_type'         => 'nullable|string|max:255',
            'bukti_kepemilikan' => 'nullable|string|max:255',
            'tahun_perolehan'   => 'required|integer|min:1900|max:2099',
            'ukuran'            => 'nullable|numeric|min:0',
            'kondisi'           => 'required|string|max:50',
            'lokasi'            => 'required|string|max:255',
            'nilai_perolehan'   => 'required|numeric|min:0',
            'sumber_dana'       => 'nullable|string|max:255',
            'penanggung_jawab'  => 'nullable|string|max:255',
            'keterangan'        => 'nullable|string',
            'detail'            => 'nullable|string', // dikirim sebagai JSON string dari frontend
            'foto'              => 'nullable|array',
            'foto.*'            => 'file|mimes:jpg,jpeg,png|max:2048', // max 2MB per foto
        ]);

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('aset', 'public');
            }
        }

        // detail dikirim sebagai JSON string dari FormData -> decode jadi array
        $detail = !empty($validated['detail']) ? json_decode($validated['detail'], true) : null;

        $aset = Aset::create([
            'nomor_aset'        => $validated['nomor_aset'] ?? null,
            'kodefikasi'        => $validated['kodefikasi'] ?? null,
            'nup'               => $validated['nup'] ?? null,
            'nama_aset'         => $validated['nama_aset'],
            'jenis_aset'        => $validated['jenis_aset'],
            'merk_type'         => $validated['merk_type'] ?? null,
            'bukti_kepemilikan' => $validated['bukti_kepemilikan'] ?? null,
            'tahun_perolehan'   => $validated['tahun_perolehan'],
            'ukuran'            => $validated['ukuran'] ?? null,
            'kondisi'           => $validated['kondisi'],
            'lokasi'            => $validated['lokasi'],
            'nilai_perolehan'   => $validated['nilai_perolehan'],
            'sumber_dana'       => $validated['sumber_dana'] ?? 'Dana Desa',
            'penanggung_jawab'  => $validated['penanggung_jawab'] ?? 'Kaur Umum',
            'keterangan'        => $validated['keterangan'] ?? null,
            'detail'            => $detail,
            'foto'              => $fotoPaths,
        ]);

        // 🔥 Auto-generate nomor register kalau dikosongkan user
        if (empty($aset->nomor_aset)) {
            $kodeJenis = [
                'Tanah'     => 'TNH',
                'Gedung'    => 'GDG',
                'Kendaraan' => 'KDR',
                'Peralatan' => 'PRL',
                'Lainnya'   => 'LNN',
            ][$aset->jenis_aset] ?? 'AST';

            $aset->nomor_aset = $kodeJenis . '-' . $aset->tahun_perolehan . '-' . str_pad($aset->id, 4, '0', STR_PAD_LEFT);
            $aset->save();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Inventaris Aset berhasil disimpan.',
            'data'    => $aset,
        ], 201);
    }

    // Fungsi buat ambil 1 data Aset (Read Detail / buat halaman Edit)
    public function show(string $id)
    {
        $aset = Aset::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $aset,
        ]);
    }

    // ====================================================================
    // PUBLIK (TANPA LOGIN) — dipakai halaman scan QR oleh warga.
    // Hanya field ringkas & aman: TIDAK menyertakan nilai perolehan,
    // sumber dana, keterangan, atau detail spesifik (no rangka/BPKB dll).
    // ====================================================================
    public function publik(string $id)
    {
        $aset = Aset::findOrFail($id);

        $foto = is_array($aset->foto) ? $aset->foto : [];

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nomor_aset'       => $aset->nomor_aset,
                'nama_aset'        => $aset->nama_aset,
                'jenis_aset'       => $aset->jenis_aset,
                'kondisi'          => $aset->kondisi,
                'lokasi'           => $aset->lokasi,
                'tahun_perolehan'  => $aset->tahun_perolehan,
                'penanggung_jawab' => $aset->penanggung_jawab,
                'foto'             => count($foto) ? [$foto[0]] : [],
            ],
        ]);
    }

    // Fungsi buat update data Aset (Update)
    public function update(Request $request, string $id)
    {
        $aset = Aset::findOrFail($id);

        $validated = $request->validate([
            'nomor_aset'        => 'nullable|string|max:255|unique:asets,nomor_aset,' . $aset->id,
            'kodefikasi'        => 'nullable|string|max:255',
            'nup'               => 'nullable|string|max:255',
            'nama_aset'         => 'sometimes|required|string|max:255',
            'jenis_aset'        => 'sometimes|required|string|max:50',
            'merk_type'         => 'nullable|string|max:255',
            'bukti_kepemilikan' => 'nullable|string|max:255',
            'tahun_perolehan'   => 'sometimes|required|integer|min:1900|max:2099',
            'ukuran'            => 'nullable|numeric|min:0',
            'kondisi'           => 'sometimes|required|string|max:50',
            'lokasi'            => 'sometimes|required|string|max:255',
            'nilai_perolehan'   => 'sometimes|required|numeric|min:0',
            'sumber_dana'       => 'nullable|string|max:255',
            'penanggung_jawab'  => 'nullable|string|max:255',
            'keterangan'        => 'nullable|string',
            'detail'            => 'nullable|string',
            'foto_baru'         => 'nullable|array',
            'foto_baru.*'       => 'file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // detail dikirim sebagai JSON string -> decode jadi array sebelum disimpan
        if (array_key_exists('detail', $validated)) {
            $validated['detail'] = !empty($validated['detail'])
                ? json_decode($validated['detail'], true)
                : null;
        }

        // Kalau ada foto baru diupload, TAMBAHKAN ke array foto yang sudah ada
        // (foto lama tidak hilang, karena Edit cuma menampilkan 1 foto utama)
        if ($request->hasFile('foto_baru')) {
            $existingRaw = $aset->getRawOriginal('foto');
            $existingFotos = $existingRaw ? json_decode($existingRaw, true) : [];

            foreach ($request->file('foto_baru') as $file) {
                $existingFotos[] = $file->store('aset', 'public');
            }
            $validated['foto'] = $existingFotos;
        }

        $aset->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Aset berhasil diperbarui.',
            'data'    => $aset,
        ]);
    }

    // Fungsi buat hapus data Aset (Delete)
    public function destroy(string $id)
    {
        $aset = Aset::findOrFail($id);

        // Hapus juga semua foto fisiknya dari storage
        $rawFotos = $aset->getRawOriginal('foto');
        $rawFotos = $rawFotos ? json_decode($rawFotos, true) : [];
        foreach (($rawFotos ?: []) as $path) {
            Storage::disk('public')->delete($path);
        }

        $aset->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Aset berhasil dihapus.',
        ]);
    }
}
