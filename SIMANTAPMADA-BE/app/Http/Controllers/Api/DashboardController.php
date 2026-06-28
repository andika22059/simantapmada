<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Arsip;
use App\Models\Aset;
use App\Models\Pelayanan;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Statistik untuk kartu-kartu dashboard.
     */
    public function statistik()
    {
        $tahun = date('Y');

        // === KARTU PENGAJUAN (PELAYANAN) ===
        // "diproses" = status apa pun yang BUKAN 'Selesai'
        // "sudah TTD" = status 'Selesai'
        $pengajuanDiproses = Pelayanan::where('status', '!=', 'Selesai')->count();
        $pengajuanSelesai  = Pelayanan::where('status', 'Selesai')->count();

        // === KARTU LAIN (jumlah data tahun berjalan) ===
        $suratMasuk  = SuratMasuk::whereYear('created_at', $tahun)->count();
        $suratKeluar = SuratKeluar::whereYear('created_at', $tahun)->count();
        $arsip       = Arsip::whereYear('created_at', $tahun)->count();
        $aset        = Aset::whereYear('created_at', $tahun)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'tahun'               => (int) $tahun,
                'pengajuan_diproses'  => $pengajuanDiproses,
                'pengajuan_selesai'   => $pengajuanSelesai,
                'surat_masuk'         => $suratMasuk,
                'surat_keluar'        => $suratKeluar,
                'arsip'               => $arsip,
                'aset'                => $aset,
            ],
        ]);
    }

    /**
     * Log aktivitas terbaru (hanya inputan/tambah).
     */
    public function aktivitas()
    {
        $logs = ActivityLog::latest()->limit(10)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $logs,
        ]);
    }

    /**
     * Statistik khusus Dashboard Sekretaris Desa (pemantau + verifikator).
     */
    public function statistikSekdes()
    {
        $tahun = date('Y');

        // Fokus Sekdes: pengajuan yang perlu tindakan verifikasi
        $menungguVerifikasi = Pelayanan::where('status', 'Diajukan')->count();
        $sudahDiverifikasi  = Pelayanan::where('status', 'Diverifikasi')->count();
        $dikembalikan       = Pelayanan::where('status', 'Dikembalikan')->count();
        $selesai            = Pelayanan::where('status', 'Selesai')->count();

        // Surat masuk yang perlu didisposisi (status_disposisi = 'Belum')
        $menungguDisposisi = SuratMasuk::where('status_disposisi', 'Belum')->count();
        $sudahDisposisi    = SuratMasuk::where('status_disposisi', 'Didisposisi')->count();

        // Data pantau (read-only)
        $suratMasuk  = SuratMasuk::whereYear('created_at', $tahun)->count();
        $suratKeluar = SuratKeluar::whereYear('created_at', $tahun)->count();
        $arsip       = Arsip::whereYear('created_at', $tahun)->count();
        $aset        = Aset::whereYear('created_at', $tahun)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'tahun'                => (int) $tahun,
                'menunggu_verifikasi'  => $menungguVerifikasi,
                'sudah_diverifikasi'   => $sudahDiverifikasi,
                'dikembalikan'         => $dikembalikan,
                'selesai'              => $selesai,
                'menunggu_disposisi'   => $menungguDisposisi,
                'sudah_disposisi'      => $sudahDisposisi,
                'surat_masuk'          => $suratMasuk,
                'surat_keluar'         => $suratKeluar,
                'arsip'                => $arsip,
                'aset'                 => $aset,
            ],
        ]);
    }

    /**
     * Statistik khusus Dashboard Kepala Desa (penyetuju akhir + pemantau).
     */
    public function statistikKades()
    {
        $tahun = date('Y');

        // Fokus Kades: pengajuan yang sudah diverifikasi Sekdes & siap di-ACC
        $menungguPersetujuan = Pelayanan::where('status', 'Diverifikasi')->count();
        $disetujui           = Pelayanan::where('status', 'Selesai')->count();

        // Surat masuk yang perlu atensi Kades (sudah didisposisi + flag atensi, belum selesai)
        $perluAtensi = SuratMasuk::where('perlu_atensi_kades', true)
            ->where('status_disposisi', 'Didisposisi')
            ->count();

        // Data pantau keseluruhan desa (tahun berjalan)
        $suratMasuk  = SuratMasuk::whereYear('created_at', $tahun)->count();
        $suratKeluar = SuratKeluar::whereYear('created_at', $tahun)->count();
        $arsip       = Arsip::whereYear('created_at', $tahun)->count();
        $aset        = Aset::whereYear('created_at', $tahun)->count();
        $totalPengajuan = Pelayanan::whereYear('created_at', $tahun)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'tahun'                 => (int) $tahun,
                'menunggu_persetujuan'  => $menungguPersetujuan,
                'disetujui'             => $disetujui,
                'perlu_atensi'          => $perluAtensi,
                'total_pengajuan'       => $totalPengajuan,
                'surat_masuk'           => $suratMasuk,
                'surat_keluar'          => $suratKeluar,
                'arsip'                 => $arsip,
                'aset'                  => $aset,
            ],
        ]);
    }

    /**
     * Rekap bulanan: ringkasan data & alur per bulan tertentu.
     * Query param: ?bulan=MM&tahun=YYYY (default bulan & tahun berjalan).
     */
    public function rekapBulanan(Request $request)
    {
        $bulanRaw = $request->query('bulan', date('n'));
        $semuaBulan = ($bulanRaw === 'semua' || $bulanRaw === '' || (int) $bulanRaw === 0);
        $bulan = $semuaBulan ? null : (int) $bulanRaw;
        $tahun = (int) $request->query('tahun', date('Y'));

        // Helper: filter per-tahun, dan per-bulan kalau bukan "Semua Bulan".
        $byPeriode = function ($query, $col) use ($semuaBulan, $bulan, $tahun) {
            $query->whereYear($col, $tahun);
            if (!$semuaBulan) {
                $query->whereMonth($col, $bulan);
            }
            return $query;
        };

        // Pengajuan surat
        $totalPengajuan = $byPeriode(Pelayanan::query(), 'created_at')->count();
        $pengajuanSelesai = $byPeriode(Pelayanan::where('status', 'Selesai'), 'updated_at')->count();
        $pengajuanDiproses = $byPeriode(Pelayanan::whereIn('status', ['Diajukan', 'Diverifikasi']), 'created_at')->count();
        $pengajuanDikembalikan = $byPeriode(Pelayanan::where('status', 'Dikembalikan'), 'created_at')->count();

        // Surat & disposisi
        $suratMasuk  = $byPeriode(SuratMasuk::query(), 'created_at')->count();
        $suratDidisposisi = $byPeriode(SuratMasuk::whereIn('status_disposisi', ['Didisposisi', 'Selesai']), 'tgl_disposisi')->count();
        $suratKeluar = $byPeriode(SuratKeluar::query(), 'created_at')->count();

        // Arsip & aset
        $arsip = $byPeriode(Arsip::query(), 'created_at')->count();
        $aset  = $byPeriode(Aset::query(), 'created_at')->count();

        // Daftar surat terbit (pengajuan selesai + nomor)
        $suratTerbit = $byPeriode(Pelayanan::where('status', 'Selesai')->whereNotNull('nomor_surat'), 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'jenis_surat', 'nomor_surat', 'updated_at']);

        // ===== DAFTAR DETAIL SEMUA MODUL =====
        $listPengajuan = $byPeriode(Pelayanan::query(), 'created_at')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'jenis_surat', 'status', 'nomor_surat', 'created_at']);

        $listSuratMasuk = $byPeriode(SuratMasuk::query(), 'created_at')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'no_surat', 'pengirim', 'perihal', 'status_disposisi', 'disposisi_ke', 'created_at']);

        $listSuratKeluar = $byPeriode(SuratKeluar::query(), 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $listArsip = $byPeriode(Arsip::query(), 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $listAset = $byPeriode(Aset::query(), 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return response()->json([
            'status' => 'success',
            'data'   => [
                'bulan'        => $semuaBulan ? 'semua' : $bulan,
                'tahun'        => $tahun,
                'nama_bulan'   => $semuaBulan ? 'Semua Bulan' : ($namaBulan[$bulan] ?? '-'),
                'pengajuan'    => [
                    'total'        => $totalPengajuan,
                    'selesai'      => $pengajuanSelesai,
                    'diproses'     => $pengajuanDiproses,
                    'dikembalikan' => $pengajuanDikembalikan,
                ],
                'surat' => [
                    'masuk'       => $suratMasuk,
                    'didisposisi' => $suratDidisposisi,
                    'keluar'      => $suratKeluar,
                ],
                'arsip'        => $arsip,
                'aset'         => $aset,
                'surat_terbit' => $suratTerbit,
                // Daftar detail tiap modul (bulan ini)
                'list' => [
                    'pengajuan'    => $listPengajuan,
                    'surat_masuk'  => $listSuratMasuk,
                    'surat_keluar' => $listSuratKeluar,
                    'arsip'        => $listArsip,
                    'aset'         => $listAset,
                ],
            ],
        ]);
    }

    /**
     * Statistik jumlah user per role (untuk Dashboard Developer).
     */
    public function statistikUser()
    {
        $perRole = \App\Models\User::selectRaw('role, COUNT(*) as jml')
            ->groupBy('role')
            ->pluck('jml', 'role');

        $totalUser = \App\Models\User::count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total'     => $totalUser,
                'admin'     => (int) ($perRole['admin'] ?? 0),
                'sekdes'    => (int) ($perRole['sekdes'] ?? 0),
                'kades'     => (int) ($perRole['kades'] ?? 0),
                'warga'     => (int) ($perRole['warga'] ?? 0),
                'developer' => (int) ($perRole['developer'] ?? 0),
            ],
        ]);
    }

    /**
     * Data untuk halaman Laporan Statistik (grafik).
     * Mengelompokkan data aset, arsip, dan pengajuan untuk chart.
     */
    public function statistikLaporan()
    {
        // Ringkasan total
        $totalAset      = Aset::count();
        $totalArsip     = Arsip::count();
        $totalPengajuan = Pelayanan::count();

        // Grafik: Aset per jenis
        $asetJenis = Aset::selectRaw('jenis_aset as label, COUNT(*) as jml')
            ->groupBy('jenis_aset')
            ->get();

        // Grafik: Aset per kondisi
        $asetKondisi = Aset::selectRaw('kondisi as label, COUNT(*) as jml')
            ->groupBy('kondisi')
            ->get();

        // Grafik: Arsip per kategori (kolom mungkin 'kategori' atau 'jenis_arsip')
        $arsipKategori = Arsip::selectRaw('kategori as label, COUNT(*) as jml')
            ->groupBy('kategori')
            ->get();

        // Grafik: Pengajuan per status
        $pengajuanStatus = Pelayanan::selectRaw('status as label, COUNT(*) as jml')
            ->groupBy('status')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total' => [
                    'aset'      => $totalAset,
                    'arsip'     => $totalArsip,
                    'pengajuan' => $totalPengajuan,
                ],
                'aset_jenis'      => $asetJenis,
                'aset_kondisi'    => $asetKondisi,
                'arsip_kategori'  => $arsipKategori,
                'pengajuan_status' => $pengajuanStatus,
            ],
        ]);
    }
}
