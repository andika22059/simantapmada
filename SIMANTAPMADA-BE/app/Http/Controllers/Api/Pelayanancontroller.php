<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pelayanan;
use Illuminate\Http\Request;

class PelayananController extends Controller
{
    public function index()
    {
        $data = Pelayanan::latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    // ============================================================
    // 🔍 AMBIL 1 DATA by ID (untuk cetak surat)
    // ============================================================
    public function show($id)
    {
        $pelayanan = Pelayanan::find($id);

        if (!$pelayanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pelayanan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $pelayanan,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string',
        ]);

        $dataDinamis = $request->except(['jenis_surat', 'keterangan', 'lampiran']);

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('pelayanan', 'public');
            }
        }

        $pelayanan = Pelayanan::create([
            'jenis_surat'    => $request->jenis_surat,
            'keterangan'     => $request->keterangan,
            'data_pengajuan' => $dataDinamis, // FIX: dulu 'data_form' (kolom DB-nya data_pengajuan)
            'lampiran'       => $lampiranPaths,
            'status'         => 'Diajukan',     // konsisten dgn default migration
        ]);

        // Catat ke log aktivitas
        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan surat "' . $pelayanan->jenis_surat . '" diajukan',
            null,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan pelayanan berhasil disimpan!',
            'data'    => $pelayanan,
        ], 201);
    }

    // ============================================================
    // ✏️ UPDATE (EDIT) oleh ADMIN — hanya selama masih "Diajukan"
    //    Begitu sudah diverifikasi Sekdes, pengajuan TERKUNCI.
    // ============================================================
    public function update(Request $request, $id)
    {
        $pelayanan = Pelayanan::find($id);
        if (!$pelayanan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // LOCK: hanya pengajuan yang masih "Diajukan" yang boleh diedit.
        // Validasi ulang di server — bukan cuma di-hide di UI.
        if ($pelayanan->status !== 'Diajukan') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pengajuan terkunci: sudah diproses Sekdes (status: ' . $pelayanan->status . '), tidak bisa diedit.',
            ], 403);
        }

        $request->validate([
            'jenis_surat' => 'required|string',
        ]);

        $dataDinamis = $request->except(['jenis_surat', 'keterangan', 'lampiran', '_method']);

        // Lampiran baru (opsional) — gabungkan dengan yang lama
        $lampiranPaths = is_array($pelayanan->lampiran) ? $pelayanan->lampiran : [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('pelayanan', 'public');
            }
        }

        $pelayanan->jenis_surat    = $request->jenis_surat;
        $pelayanan->keterangan     = $request->keterangan;
        $pelayanan->data_pengajuan = $dataDinamis;
        $pelayanan->lampiran       = $lampiranPaths;
        $pelayanan->save();

        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan "' . $pelayanan->jenis_surat . '" diedit admin',
            null,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan berhasil diperbarui.',
            'data'    => $pelayanan,
        ]);
    }

    // ============================================================
    // 🔍 VERIFIKASI oleh SEKDES: Diajukan → Diverifikasi
    // ============================================================
    public function verifikasi(Request $request, $id)
    {
        $pelayanan = Pelayanan::find($id);
        if (!$pelayanan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // Hanya pengajuan yang masih "Diajukan" yang bisa diverifikasi
        if ($pelayanan->status !== 'Diajukan') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pengajuan ini sudah diproses (status: ' . $pelayanan->status . ').',
            ], 422);
        }

        $pelaku = optional($request->user())->nama_lengkap ?? 'Sekretaris Desa';

        $pelayanan->status            = 'Diverifikasi';
        $pelayanan->diverifikasi_oleh = $pelaku;
        $pelayanan->tgl_verifikasi    = now();
        $pelayanan->catatan           = null; // bersihkan catatan kalau sebelumnya pernah dikembalikan
        $pelayanan->save();

        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan "' . $pelayanan->jenis_surat . '" diverifikasi Sekdes',
            null,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan berhasil diverifikasi dan diteruskan ke Kepala Desa.',
            'data'    => $pelayanan,
        ]);
    }

    // ============================================================
    // ↩️ KEMBALIKAN oleh SEKDES: Diajukan → Dikembalikan (+ alasan)
    // ============================================================
    public function kembalikan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $pelayanan = Pelayanan::find($id);
        if (!$pelayanan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        if (!in_array($pelayanan->status, ['Diajukan', 'Diverifikasi'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pengajuan ini tidak bisa dikembalikan (status: ' . $pelayanan->status . ').',
            ], 422);
        }

        $pelayanan->status  = 'Dikembalikan';
        $pelayanan->catatan = $request->catatan;
        $pelayanan->save();

        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan "' . $pelayanan->jenis_surat . '" dikembalikan Sekdes',
            null,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan dikembalikan dengan catatan.',
            'data'    => $pelayanan,
        ]);
    }

    // ============================================================
    // ✍️ ACC + NOMOR SURAT oleh KADES: Diverifikasi → Selesai
    // ============================================================
    public function updateStatus(Request $request, $id)
    {
        $pelayanan = Pelayanan::find($id);

        if (!$pelayanan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $statusBaru = $request->status;

        // Kades hanya boleh meng-ACC (Selesai) pengajuan yang SUDAH diverifikasi Sekdes
        if ($statusBaru === 'Selesai' && $pelayanan->status !== 'Diverifikasi') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pengajuan harus diverifikasi Sekdes terlebih dahulu sebelum disetujui.',
            ], 422);
        }

        // Generate nomor surat otomatis saat Selesai & belum punya nomor
        if ($statusBaru === 'Selesai' && empty($pelayanan->nomor_surat)) {
            $tahun     = date('Y');
            $kodeDepan = '470';
            $kodeDesa  = '411.508.21';

            $suratTerakhir = Pelayanan::where('jenis_surat', $pelayanan->jenis_surat)
                ->where('status', 'Selesai')
                ->whereYear('updated_at', $tahun)
                ->whereNotNull('nomor_surat')
                ->orderBy('id', 'desc')
                ->first();

            if ($suratTerakhir) {
                $pecahNomor = explode(' / ', $suratTerakhir->nomor_surat);
                $urutanBaru = (int) ($pecahNomor[1] ?? 0) + 1;
            } else {
                $urutanBaru = 1;
            }

            $nomorUrutFormat = str_pad($urutanBaru, 3, '0', STR_PAD_LEFT);
            $pelayanan->nomor_surat = "{$kodeDepan} / {$nomorUrutFormat} / {$kodeDesa} / {$tahun}";

            $pelayanan->disetujui_oleh  = optional($request->user())->nama_lengkap ?? 'Kepala Desa';
            $pelayanan->tgl_persetujuan = now();
        }

        $pelayanan->status = $statusBaru;
        $pelayanan->save();

        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan "' . $pelayanan->jenis_surat . '" disetujui & diberi nomor',
            $pelayanan->nomor_surat,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat berhasil di-ACC dan diberi nomor!',
            'data'    => $pelayanan,
        ]);
    }

    // ============================================================
    // ❌ TOLAK oleh KADES: Diverifikasi → Ditolak (+ alasan)
    // ============================================================
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $pelayanan = Pelayanan::find($id);
        if (!$pelayanan) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // Hanya pengajuan yang sudah diverifikasi Sekdes yang bisa ditolak Kades
        if ($pelayanan->status !== 'Diverifikasi') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya pengajuan yang sudah diverifikasi yang bisa ditolak (status: ' . $pelayanan->status . ').',
            ], 422);
        }

        $pelayanan->status         = 'Ditolak';
        $pelayanan->catatan        = $request->catatan;
        $pelayanan->disetujui_oleh = optional($request->user())->nama_lengkap ?? 'Kepala Desa';
        $pelayanan->tgl_persetujuan = now();
        $pelayanan->save();

        ActivityLog::catat(
            'Pelayanan',
            'Pengajuan "' . $pelayanan->jenis_surat . '" DITOLAK Kepala Desa',
            null,
            $request
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan ditolak dengan alasan.',
            'data'    => $pelayanan,
        ]);
    }
}
