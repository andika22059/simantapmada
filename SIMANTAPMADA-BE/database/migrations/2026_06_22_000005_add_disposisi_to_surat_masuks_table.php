<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom disposisi ke surat_masuks (additive, aman).
     * Alur: Belum → Didisposisi → Selesai.
     * Catatan: kolom 'disposisi_ke' SUDAH ADA, jadi tidak dibuat ulang.
     */
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuks', 'status_disposisi')) {
                $table->string('status_disposisi')->default('Belum')->after('keterangan');
            }
            if (!Schema::hasColumn('surat_masuks', 'instruksi')) {
                $table->text('instruksi')->nullable()->after('status_disposisi');
            }
            if (!Schema::hasColumn('surat_masuks', 'perlu_atensi_kades')) {
                $table->boolean('perlu_atensi_kades')->default(false)->after('instruksi');
            }
            if (!Schema::hasColumn('surat_masuks', 'didisposisi_oleh')) {
                $table->string('didisposisi_oleh')->nullable()->after('perlu_atensi_kades');
            }
            if (!Schema::hasColumn('surat_masuks', 'tgl_disposisi')) {
                $table->timestamp('tgl_disposisi')->nullable()->after('didisposisi_oleh');
            }
            if (!Schema::hasColumn('surat_masuks', 'atensi_kades')) {
                $table->string('atensi_kades')->nullable()->after('tgl_disposisi');
            }
            if (!Schema::hasColumn('surat_masuks', 'tgl_atensi')) {
                $table->timestamp('tgl_atensi')->nullable()->after('atensi_kades');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            foreach (
                ['status_disposisi', 'instruksi', 'perlu_atensi_kades', 'didisposisi_oleh', 'tgl_disposisi', 'atensi_kades', 'tgl_atensi']
                as $kolom
            ) {
                if (Schema::hasColumn('surat_masuks', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
