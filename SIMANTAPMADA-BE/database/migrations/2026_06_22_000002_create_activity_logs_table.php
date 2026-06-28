<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel log aktivitas (audit trail).
     * Mencatat setiap inputan data baru dari seluruh modul
     * (Surat Masuk, Surat Keluar, Arsip, Aset, Pelayanan).
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('modul');        // contoh: "Surat Masuk", "Aset"
            $table->string('aksi')->default('Input'); // jenis aksi (Input)
            $table->text('deskripsi');      // ringkasan: "Surat dari Kecamatan ditambahkan"
            $table->string('referensi')->nullable(); // nomor surat / nomor aset / kode, dll
            $table->string('pelaku')->nullable();     // nama user yang melakukan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
