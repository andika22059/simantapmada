<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index()
    {
        $arsip = Arsip::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $arsip,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_arsip'    => 'required|string|max:255',
            'pencipta'       => 'nullable|string|max:255',
            'judul'          => 'required|string|max:255',
            'kategori'       => 'required|string|max:255',
            'lokasi_fisik'   => 'nullable|string|max:255',
            'status'         => 'nullable|string|max:50',
            'durasi_retensi' => 'nullable|string|max:50',
            'tgl_retensi'    => 'nullable|date',
            'nasib_akhir'    => 'nullable|string|max:50',
            'files'          => 'required|array|min:1',
            'files.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB per file
        ]);

        // Simpan tiap file ke storage/app/public/arsip, kumpulkan path-nya
        $storedPaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $storedPaths[] = $file->store('arsip', 'public');
            }
        }

        $arsip = Arsip::create([
            'nomor_arsip'    => $validated['nomor_arsip'],
            'pencipta'       => $validated['pencipta'] ?? null,
            'judul'          => $validated['judul'],
            'kategori'       => $validated['kategori'],
            'lokasi_fisik'   => $validated['lokasi_fisik'] ?? null,
            'status'         => $validated['status'] ?? 'Aktif',
            'durasi_retensi' => $validated['durasi_retensi'] ?? null,
            'tgl_retensi'    => $validated['tgl_retensi'] ?? null,
            'nasib_akhir'    => $validated['nasib_akhir'] ?? 'Musnah',
            'files'          => $storedPaths,
        ]);

        return response()->json([
            'message' => 'Arsip berhasil disimpan.',
            'data'    => $arsip,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $arsip = Arsip::findOrFail($id);

        return response()->json([
            'data' => $arsip,
        ]);
    }

    // ====================================================================
    // PUBLIK (TANPA LOGIN) — dipakai halaman scan QR oleh warga.
    // Untuk verifikasi keaslian arsip. TIDAK menyertakan file/dokumen
    // digital maupun lokasi simpan fisik (informasi internal).
    // ====================================================================
    public function publik(string $id)
    {
        $arsip = Arsip::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nomor_arsip' => $arsip->nomor_arsip,
                'judul'       => $arsip->judul,
                'kategori'    => $arsip->kategori,
                'pencipta'    => $arsip->pencipta,
                'status'      => $arsip->status,
                'tgl_retensi' => $arsip->tgl_retensi
                    ? $arsip->tgl_retensi->format('Y-m-d')
                    : null,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $arsip = Arsip::findOrFail($id);

        $validated = $request->validate([
            'nomor_arsip'    => 'sometimes|required|string|max:255',
            'pencipta'       => 'nullable|string|max:255',
            'judul'          => 'sometimes|required|string|max:255',
            'kategori'       => 'sometimes|required|string|max:255',
            'lokasi_fisik'   => 'nullable|string|max:255',
            'status'         => 'nullable|string|max:50',
            'durasi_retensi' => 'nullable|string|max:50',
            'tgl_retensi'    => 'nullable|date',
            'nasib_akhir'    => 'nullable|string|max:50',
        ]);

        $arsip->update($validated);

        return response()->json([
            'message' => 'Arsip berhasil diperbarui.',
            'data'    => $arsip,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $arsip = Arsip::findOrFail($id);

        // Hapus juga file fisiknya dari storage biar tidak jadi sampah
        if (!empty($arsip->files)) {
            foreach ($arsip->files as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $arsip->delete();

        return response()->json([
            'message' => 'Arsip berhasil dihapus.',
        ]);
    }
}
