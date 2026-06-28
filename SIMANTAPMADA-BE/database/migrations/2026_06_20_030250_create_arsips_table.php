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
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_arsip');
            $table->string('pencipta')->nullable();
            $table->string('judul');
            $table->string('kategori');
            $table->string('lokasi_fisik')->nullable();
            $table->string('status')->default('Aktif');
            $table->string('durasi_retensi')->nullable();
            $table->date('tgl_retensi')->nullable();
            $table->string('nasib_akhir')->default('Musnah');
            $table->json('files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
