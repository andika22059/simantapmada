<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AsetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\PelayananController;
use App\Http\Controllers\Api\ArsipController;
use App\Http\Controllers\Api\SuratKeluarController;
use App\Http\Controllers\Api\PengaturanController;

// =====================================================
// Auth (login terbuka, sisanya butuh token)
// =====================================================
Route::post('/login', [AuthController::class, 'login']);

// =====================================================
// Waktu server (WIB) — dipakai halaman login untuk menentukan
// tema pagi/siang/sore/malam. Diambil dari server supaya tidak
// bisa dicurangi dengan mengubah jam perangkat.
// Timezone aplikasi masih UTC, jadi dikonversi eksplisit ke WIB.
// =====================================================
Route::get('/waktu', function () {
    $sekarang = now()->setTimezone('Asia/Jakarta');

    return response()->json([
        'waktu' => $sekarang->toIso8601String(),
        'jam'   => (int) $sekarang->format('G'), // 0-23
    ]);
});

// =====================================================
// PUBLIK (TANPA LOGIN) — untuk scan QR oleh warga.
// Hanya mengembalikan data ringkas & aman (tanpa file/dokumen,
// nilai aset, lokasi simpan fisik, atau catatan internal).
// =====================================================
Route::get('/publik/aset/{id}',  [AsetController::class, 'publik']);
Route::get('/publik/arsip/{id}', [ArsipController::class, 'publik']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Profil (update data diri + ganti password)
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);

    // Pengaturan instansi (kop, nama & TTD Kades) — bisa diubah tanpa bongkar BE
    Route::get('/pengaturan', [PengaturanController::class, 'show']);
    Route::post('/pengaturan', [PengaturanController::class, 'update']);

    // =====================================================
    // Dashboard (statistik kartu + log aktivitas)
    // =====================================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistik', [DashboardController::class, 'statistik']);
        Route::get('/aktivitas', [DashboardController::class, 'aktivitas']);
        Route::get('/statistik-sekdes', [DashboardController::class, 'statistikSekdes']);
        Route::get('/statistik-kades', [DashboardController::class, 'statistikKades']);
        Route::get('/statistik-user', [DashboardController::class, 'statistikUser']);
        Route::get('/rekap-bulanan', [DashboardController::class, 'rekapBulanan']);
        Route::get('/statistik-laporan', [DashboardController::class, 'statistikLaporan']);
    });

    // =====================================================
    // Aset
    // =====================================================
    Route::prefix('aset')->group(function () {
        Route::get('/',        [AsetController::class, 'index']);
        Route::post('/',       [AsetController::class, 'store']);
        Route::get('/{id}',    [AsetController::class, 'show']);
        Route::post('/{id}',   [AsetController::class, 'update']); // POST bukan PUT karena ada upload foto
        Route::delete('/{id}', [AsetController::class, 'destroy']);
    });

    // =====================================================
    // Surat Masuk
    // =====================================================
    Route::prefix('surat-masuk')->group(function () {
        Route::get('/',  [SuratMasukController::class, 'index']);
        Route::post('/', [SuratMasukController::class, 'store']);
        Route::get('/{id}', [SuratMasukController::class, 'show']);                     // Ambil 1 data (edit)
        Route::post('/{id}', [SuratMasukController::class, 'update']);                  // Update (POST krn upload file)
        Route::put('/{id}/disposisi', [SuratMasukController::class, 'disposisi']);     // Sekdes
        Route::put('/{id}/atensi',    [SuratMasukController::class, 'atensiKades']);   // Kades
    });

    // =====================================================
    // Pelayanan (Pengajuan Surat)
    // =====================================================
    Route::prefix('pelayanan')->group(function () {
        Route::get('/',                [PelayananController::class, 'index']);
        Route::post('/',               [PelayananController::class, 'store']);
        Route::put('/{id}/status',     [PelayananController::class, 'updateStatus']);   // Kades: ACC
        Route::put('/{id}/tolak',      [PelayananController::class, 'tolak']);          // Kades: Tolak (+ alasan)
        Route::put('/{id}/verifikasi', [PelayananController::class, 'verifikasi']);     // Sekdes: verifikasi
        Route::put('/{id}/kembalikan', [PelayananController::class, 'kembalikan']);
        Route::put('/{id}',            [PelayananController::class, 'update']);          // Admin: edit (terkunci kalau bukan Diajukan)
        Route::get('/{id}',            [PelayananController::class, 'show']);            // Ambil 1 data (cetak surat)
    });

    // =====================================================
    // Arsip
    // =====================================================
    Route::prefix('arsip')->group(function () {
        Route::get('/',        [ArsipController::class, 'index']);
        Route::post('/',       [ArsipController::class, 'store']);
        Route::get('/{id}',    [ArsipController::class, 'show']);
        Route::put('/{id}',    [ArsipController::class, 'update']);
        Route::delete('/{id}', [ArsipController::class, 'destroy']);
    });

    // =====================================================
    // Surat Keluar
    // =====================================================
    Route::prefix('surat-keluar')->group(function () {
        Route::get('/',        [SuratKeluarController::class, 'index']);
        Route::post('/',       [SuratKeluarController::class, 'store']);
        Route::get('/{id}',    [SuratKeluarController::class, 'show']);
        Route::put('/{id}',    [SuratKeluarController::class, 'update']);
        Route::delete('/{id}', [SuratKeluarController::class, 'destroy']);
    });
});
