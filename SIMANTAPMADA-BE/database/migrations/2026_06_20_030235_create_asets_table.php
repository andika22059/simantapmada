<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_aset')->unique()->nullable(); // Nomor register
            $table->string('kodefikasi')->nullable();            // Kode barang (Permendagri)
            $table->string('nup')->nullable();                   // Nomor Urut Pendaftaran
            $table->string('nama_aset');
            $table->string('jenis_aset');
            $table->string('merk_type')->nullable();             // Merk / Type barang
            $table->string('bukti_kepemilikan')->nullable();     // Bukti kepemilikan
            $table->unsignedSmallInteger('tahun_perolehan');
            $table->decimal('ukuran', 15, 2)->nullable();        // Ukuran dalam m2
            $table->string('kondisi')->default('Baik');
            $table->string('lokasi')->nullable();
            $table->decimal('nilai_perolehan', 18, 2)->default(0);
            $table->string('sumber_dana')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->json('detail')->nullable();                  // 🔥 field spesifik per jenis aset
            $table->json('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
};
