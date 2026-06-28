<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk alur verifikasi Sekdes + TTD Kades (additive, aman).
     * Alur: Diajukan → Diverifikasi (Sekdes) → Selesai (Kades).
     */
    public function up(): void
    {
        Schema::table('pelayanans', function (Blueprint $table) {
            // Nomor surat resmi (diisi saat Kades ACC) — sebelumnya dipakai di controller tapi belum ada kolomnya
            if (!Schema::hasColumn('pelayanans', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after('status');
            }
            // Jejak verifikasi Sekdes
            if (!Schema::hasColumn('pelayanans', 'diverifikasi_oleh')) {
                $table->string('diverifikasi_oleh')->nullable()->after('nomor_surat');
            }
            if (!Schema::hasColumn('pelayanans', 'tgl_verifikasi')) {
                $table->timestamp('tgl_verifikasi')->nullable()->after('diverifikasi_oleh');
            }
            // Jejak persetujuan Kades
            if (!Schema::hasColumn('pelayanans', 'disetujui_oleh')) {
                $table->string('disetujui_oleh')->nullable()->after('tgl_verifikasi');
            }
            if (!Schema::hasColumn('pelayanans', 'tgl_persetujuan')) {
                $table->timestamp('tgl_persetujuan')->nullable()->after('disetujui_oleh');
            }
            // Catatan kalau pengajuan dikembalikan (alasan)
            if (!Schema::hasColumn('pelayanans', 'catatan')) {
                $table->text('catatan')->nullable()->after('tgl_persetujuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pelayanans', function (Blueprint $table) {
            foreach (
                ['nomor_surat', 'diverifikasi_oleh', 'tgl_verifikasi', 'disetujui_oleh', 'tgl_persetujuan', 'catatan']
                as $kolom
            ) {
                if (Schema::hasColumn('pelayanans', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
