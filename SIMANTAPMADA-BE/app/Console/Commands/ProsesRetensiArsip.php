<?php

namespace App\Console\Commands;

use App\Models\Arsip;
use Illuminate\Console\Command;

/**
 * Proses Jadwal Retensi Arsip (JRA) — alur ANRI.
 *
 * Alur status:
 *   Aktif  --(tgl_retensi terlewat)-->  Inaktif / Siap Susut
 *
 * Saat masa retensi (tgl_retensi) sudah lewat, arsip otomatis ditandai
 * sesuai "nasib_akhir"-nya:
 *   - Musnah          -> status "Siap Musnah"
 *   - Permanen        -> status "Permanen" (dipindah ke arsip statis)
 *   - Dinilai Kembali -> status "Dinilai Kembali"
 *
 * Arsip yang sudah final (sudah ditandai) tidak diproses ulang.
 */
class ProsesRetensiArsip extends Command
{
    protected $signature = 'arsip:proses-retensi';
    protected $description = 'Tandai arsip yang masa retensinya (JRA) sudah terlewat sesuai nasib akhir (ANRI).';

    public function handle(): int
    {
        $hariIni = now()->toDateString();

        // Hanya arsip yang masih dalam siklus aktif & sudah punya tanggal retensi yang lewat
        $kandidat = Arsip::whereNotNull('tgl_retensi')
            ->whereDate('tgl_retensi', '<=', $hariIni)
            ->whereIn('status', ['Aktif', 'Inaktif'])
            ->get();

        $jumlah = 0;
        foreach ($kandidat as $arsip) {
            $nasib = strtolower((string) $arsip->nasib_akhir);

            if (str_contains($nasib, 'permanen')) {
                $arsip->status = 'Permanen';
            } elseif (str_contains($nasib, 'nilai')) {
                $arsip->status = 'Dinilai Kembali';
            } else {
                // default ANRI: musnah
                $arsip->status = 'Siap Musnah';
            }

            $arsip->save();
            $jumlah++;
        }

        $this->info("Proses retensi selesai. {$jumlah} arsip diperbarui statusnya.");
        return self::SUCCESS;
    }
}
