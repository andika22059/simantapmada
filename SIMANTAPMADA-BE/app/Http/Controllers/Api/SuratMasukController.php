<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Arsip;
use App\Models\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratMasukController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => SuratMasuk::latest()->get()]);
    }

    // ============================================================
    // 🔍 AMBIL 1 DATA by ID (untuk halaman edit)
    // ============================================================
    public function show($id)
    {
        $surat = SuratMasuk::find($id);
        if (!$surat) {
            return response()->json(['status' => 'error', 'message' => 'Surat tidak ditemukan'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $surat]);
    }

    // ============================================================
    // ✏️ UPDATE SURAT MASUK (hanya yang belum didisposisi)
    // ============================================================
    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::find($id);
        if (!$surat) {
            return response()->json(['status' => 'error', 'message' => 'Surat tidak ditemukan'], 404);
        }

        // Kunci: surat yang sudah didisposisi tidak boleh diedit
        if ($surat->status_disposisi !== 'Belum') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Surat yang sudah didisposisi tidak dapat diedit.',
            ], 422);
        }

        $request->validate([
            'no_surat' => 'required',
            'pengirim' => 'required',
            'perihal'  => 'required',
            'file'     => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120', // file opsional saat edit
        ]);

        // Kalau ada file baru, ganti; kalau tidak, pakai file lama
        if ($request->hasFile('file')) {
            $surat->file_surat = $request->file('file')->store('surat-masuk', 'public');
        }

        $surat->no_surat       = $request->no_surat;
        $surat->pengirim       = $request->pengirim;
        $surat->perihal        = $request->perihal;
        $surat->tanggal_surat  = $request->tanggal_surat;
        $surat->tanggal_terima = $request->tanggal_terima;
        $surat->jenis_surat    = $request->jenis_surat;
        $surat->sifat          = $request->sifat;
        $surat->alamat         = $request->alamat;
        $surat->keterangan     = $request->keterangan;
        $surat->save();

        ActivityLog::catat(
            'Surat Masuk',
            'Surat masuk "' . $surat->perihal . '" diperbarui',
            $surat->no_surat,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat masuk berhasil diperbarui!',
            'data'    => $surat,
        ]);
    }

    public function store(Request $request)
    {
        // Cek file wajib
        $request->validate([
            'no_surat' => 'required',
            'pengirim' => 'required',
            'perihal'  => 'required',
            'file'     => 'required|file|mimes:pdf,jpg,png,jpeg|max:5120', // Max 5MB
            'simpan_arsip' => 'nullable|boolean',
        ]);

        // Simpan file ke folder storage/app/public/surat-masuk
        $filePath = $request->file('file')->store('surat-masuk', 'public');

        // 🔥 DB transaction: surat masuk + (opsional) arsip dibuat bareng.
        // Kalau salah satu gagal, semuanya rollback otomatis.
        $surat = DB::transaction(function () use ($request, $filePath) {
            // Masukin SEMUA data ke Database
            $surat = SuratMasuk::create([
                'no_surat'       => $request->no_surat,
                'pengirim'       => $request->pengirim,
                'perihal'        => $request->perihal,
                'tanggal_surat'  => $request->tanggal_surat,
                'file_surat'     => $filePath,

                // 🔥 INI HARUS ADA BIAR NYAMBUNG KE DATABASE
                'tanggal_terima' => $request->tanggal_terima,
                'jenis_surat'    => $request->jenis_surat,
                'sifat'          => $request->sifat,
                'disposisi_ke'   => $request->disposisi_ke,
                'alamat'         => $request->alamat,
                'keterangan'     => $request->keterangan,
            ]);

            // 🔥 TOGGLE "Simpan ke E-Arsip" -> bikin record arsip otomatis
            // pakai file yang sama, gak perlu upload ulang.
            if ($request->boolean('simpan_arsip')) {
                Arsip::create([
                    'nomor_arsip'    => $surat->no_surat,
                    'pencipta'       => optional($request->user())->name ?? 'Admin Desa',
                    'judul'          => $surat->perihal,
                    'kategori'       => 'Surat Masuk',
                    'lokasi_fisik'   => 'Digital (E-Arsip)',
                    'status'         => 'Aktif',
                    'durasi_retensi' => '5', // default 5 tahun, sesuaikan kalau perlu
                    'tgl_retensi'    => Carbon::parse($surat->tanggal_surat)->addYears(5)->format('Y-m-d'),
                    'nasib_akhir'    => 'Musnah',
                    'files'          => [$filePath],
                ]);
            }

            return $surat;
        });

        // 📝 Catat ke log aktivitas
        ActivityLog::catat(
            'Surat Masuk',
            'Surat masuk dari ' . $surat->pengirim . ' diinput',
            $surat->no_surat,
            $request
        );

        return response()->json(['status' => 'success', 'message' => 'Surat masuk berhasil disimpan!', 'data' => $surat]);
    }

    // ============================================================
    // 📨 DISPOSISI oleh SEKDES: Belum → Didisposisi
    // ============================================================
    public function disposisi(Request $request, $id)
    {
        $validated = $request->validate([
            'disposisi_ke'       => 'required|string|max:255',
            'instruksi'          => 'nullable|string|max:1000',
            'perlu_atensi_kades' => 'nullable|boolean',
        ]);

        $surat = SuratMasuk::find($id);
        if (!$surat) {
            return response()->json(['status' => 'error', 'message' => 'Surat tidak ditemukan'], 404);
        }

        $pelaku = optional($request->user())->nama_lengkap ?? 'Sekretaris Desa';

        $surat->disposisi_ke       = $validated['disposisi_ke'];
        $surat->instruksi          = $validated['instruksi'] ?? null;
        $surat->perlu_atensi_kades = $request->boolean('perlu_atensi_kades');
        $surat->status_disposisi   = 'Didisposisi';
        $surat->didisposisi_oleh   = $pelaku;
        $surat->tgl_disposisi      = now();
        $surat->save();

        $tujuanInfo = $surat->perlu_atensi_kades
            ? $surat->disposisi_ke . ' (perlu atensi Kepala Desa)'
            : $surat->disposisi_ke;

        ActivityLog::catat(
            'Surat Masuk',
            'Surat "' . $surat->perihal . '" didisposisi ke ' . $tujuanInfo,
            $surat->no_surat,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat berhasil didisposisikan ke ' . $surat->disposisi_ke . '.',
            'data'    => $surat,
        ]);
    }

    // ============================================================
    // ✅ ATENSI + TERUSKAN oleh KADES: beri arahan + teruskan ke Kaur/Kasi → Selesai
    // ============================================================
    public function atensiKades(Request $request, $id)
    {
        $validated = $request->validate([
            'diteruskan_ke' => 'required|string|max:255',
            'arahan_kades'  => 'nullable|string|max:1000',
        ]);

        $surat = SuratMasuk::find($id);
        if (!$surat) {
            return response()->json(['status' => 'error', 'message' => 'Surat tidak ditemukan'], 404);
        }

        if (!$surat->perlu_atensi_kades) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Surat ini tidak memerlukan atensi Kepala Desa.',
            ], 422);
        }

        $pelaku = optional($request->user())->nama_lengkap ?? 'Kepala Desa';

        $surat->atensi_kades     = 'Telah ditinjau & diteruskan oleh ' . $pelaku;
        $surat->tgl_atensi       = now();
        $surat->diteruskan_ke    = $validated['diteruskan_ke'];
        $surat->arahan_kades     = $validated['arahan_kades'] ?? null;
        $surat->status_disposisi = 'Selesai';
        $surat->save();

        ActivityLog::catat(
            'Surat Masuk',
            'Surat "' . $surat->perihal . '" diteruskan Kades ke ' . $surat->diteruskan_ke,
            $surat->no_surat,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Arahan diberikan & surat diteruskan ke ' . $surat->diteruskan_ke . '.',
            'data'    => $surat,
        ]);
    }
}
