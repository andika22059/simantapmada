<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->date('tanggal_terima')->nullable();
            $table->string('jenis_surat')->nullable();
            $table->string('sifat')->nullable();
            $table->string('disposisi_ke')->nullable();
            $table->string('alamat')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn(['tanggal_terima', 'jenis_surat', 'sifat', 'disposisi_ke', 'alamat', 'keterangan']);
        });
    }
};
