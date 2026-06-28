<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use App\Models\SuratKeluar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $data = SuratKeluar::orderBy('tanggal_surat', 'desc')->get()->map(function ($item) {
            if ($item->file_surat && !str_starts_with($item->file_surat, 'http')) {
                $item->file_surat = asset('storage/' . $item->file_surat);
            }

            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi cocokin persis sama Database
        $validated = $request->validate([
            'nomor_surat'   => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tujuan'        => 'required|string|max:255',
            'perihal'       => 'required|string',
            'keterangan'    => 'nullable|string',
            'file_surat'    => 'required|file|mimes:pdf|max:5120', // max 5MB
            'simpan_arsip'  => 'nullable|boolean',
        ]);

        $path = $request->file('file_surat')->store('surat-keluar', 'public');


        $surat = DB::transaction(function () use ($validated, $path, $request) {
            $surat = SuratKeluar::create([
                'nomor_surat'   => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan'        => $validated['tujuan'],
                'perihal'       => $validated['perihal'],
                'file_surat'    => $path,
                'keterangan'    => $validated['keterangan'] ?? '-',
            ]);


            if ($request->boolean('simpan_arsip')) {
                Arsip::create([
                    'nomor_arsip'    => $surat->nomor_surat,
                    'pencipta'       => optional($request->user())->name ?? 'Admin Desa',
                    'judul'          => $surat->perihal,
                    'kategori'       => 'Surat Keluar',
                    'lokasi_fisik'   => 'Digital (E-Arsip)',
                    'status'         => 'Aktif',
                    'durasi_retensi' => '5', // default 5 tahun, sesuaikan kalau perlu
                    'tgl_retensi'    => Carbon::parse($surat->tanggal_surat)->addYears(5)->format('Y-m-d'),
                    'nasib_akhir'    => 'Musnah',
                    'files'          => [$path],
                ]);
            }

            return $surat;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat Keluar berhasil disimpan.',
            'data'    => $surat,
        ], 201);
    }

    public function show(string $id)
    {
        $surat = SuratKeluar::findOrFail($id);

        if ($surat->file_surat && !str_starts_with($surat->file_surat, 'http')) {
            $surat->file_surat = asset('storage/' . $surat->file_surat);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $surat,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $surat = SuratKeluar::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat'   => 'sometimes|required|string|max:255',
            'tanggal_surat' => 'sometimes|required|date',
            'tujuan'        => 'sometimes|required|string|max:255',
            'perihal'       => 'sometimes|required|string',
            'keterangan'    => 'sometimes|nullable|string',
        ]);

        $surat->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat Keluar berhasil diperbarui.',
            'data'    => $surat,
        ]);
    }

    public function destroy(string $id)
    {
        $surat = SuratKeluar::findOrFail($id);

        if ($surat->file_surat) {
            $path = str_replace(asset('storage/'), '', $surat->file_surat);
            Storage::disk('public')->delete($path);
        }

        $surat->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat Keluar beserta PDF berhasil dihapus.',
        ]);
    }
}
