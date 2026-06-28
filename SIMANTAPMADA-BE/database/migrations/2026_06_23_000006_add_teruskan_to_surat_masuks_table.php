<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk disposisi berjenjang:
     * Kades memberi arahan + meneruskan disposisi ke Kaur/Kasi (additive, aman).
     */
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuks', 'diteruskan_ke')) {
                $table->string('diteruskan_ke')->nullable()->after('tgl_atensi');
            }
            if (!Schema::hasColumn('surat_masuks', 'arahan_kades')) {
                $table->text('arahan_kades')->nullable()->after('diteruskan_ke');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            foreach (['diteruskan_ke', 'arahan_kades'] as $kolom) {
                if (Schema::hasColumn('surat_masuks', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
