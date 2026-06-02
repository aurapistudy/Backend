<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiksiController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\MateriBabController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SesiBacaController;
use App\Http\Controllers\RakBukuController;
use App\Http\Controllers\ApiKuisController;
use App\Http\Controllers\ApiCatatanController;
use App\Http\Controllers\ApiPanduanController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\AacController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes mirror the API plan in API_DOCUMENTATION.md / SETUP_API_ROUTES.md.
| They assume Sanctum is installed and the controllers expose API methods.
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/forgot-password', [AuthController::class, 'apiForgotPassword']);
Route::post('/reset-password', [AuthController::class, 'apiResetPassword']);
// Panduan
    Route::get('/panduan', [ApiPanduanController::class, 'index']);
// AAC (public read-only)
Route::get('/aac', [AacController::class, 'index']);
Route::get('/aac/{id}', [AacController::class, 'show']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'apiIndex']);

    // Resources (prefix route names to avoid collision with web routes)
    Route::name('api.')->group(function () {
        Route::apiResource('materi', MateriController::class);
        Route::get('materi/{materi}/bab', [MateriBabController::class, 'index'])->name('materi.bab.index');
        Route::post('materi/{materi}/bab', [MateriBabController::class, 'store'])->name('materi.bab.store');
        Route::get('materi/{materi}/bab/{bab}', [MateriBabController::class, 'show'])->name('materi.bab.show');
        Route::put('materi/{materi}/bab/{bab}', [MateriBabController::class, 'update'])->name('materi.bab.update');
        Route::delete('materi/{materi}/bab/{bab}', [MateriBabController::class, 'destroy'])->name('materi.bab.destroy');
        Route::post('materi/{materi}/bab/{bab}/generate-summary', [MateriBabController::class, 'generateSummary'])->name('materi.bab.generate-summary');
        // Also expose the dashboard-prefixed API endpoints so frontend
        // code that calls `/api/dashboard/materi` continues to work.
        Route::apiResource('dashboard/materi', MateriController::class)->names([
            'index' => 'dashboard.materi.index',
            'store' => 'dashboard.materi.store',
            'show' => 'dashboard.materi.show',
            'update' => 'dashboard.materi.update',
            'destroy' => 'dashboard.materi.destroy',
        ]);
        Route::get('dashboard/materi/{materi}/bab', [MateriBabController::class, 'index'])->name('dashboard.materi.bab.index');
        Route::post('dashboard/materi/{materi}/bab', [MateriBabController::class, 'store'])->name('dashboard.materi.bab.store');
        Route::get('dashboard/materi/{materi}/bab/{bab}', [MateriBabController::class, 'show'])->name('dashboard.materi.bab.show');
        Route::put('dashboard/materi/{materi}/bab/{bab}', [MateriBabController::class, 'update'])->name('dashboard.materi.bab.update');
        Route::delete('dashboard/materi/{materi}/bab/{bab}', [MateriBabController::class, 'destroy'])->name('dashboard.materi.bab.destroy');
        Route::post('dashboard/materi/{materi}/bab/{bab}/generate-summary', [MateriBabController::class, 'generateSummary'])->name('dashboard.materi.bab.generate-summary');
        Route::apiResource('fiksi', FiksiController::class);
        Route::apiResource('pengguna', PenggunaController::class);
        Route::apiResource('level', LevelController::class);
        Route::apiResource('mata-pelajaran', MataPelajaranController::class);
    });

    // Additional endpoints
    Route::get('/level/aktif', [LevelController::class, 'aktif']);
    Route::get('/mata-pelajaran/aktif', [MataPelajaranController::class, 'aktif']);

    // Profile (same paths used by web routes; return JSON when called via API)
    Route::get('/dashboard/profile', [ProfileController::class, 'index']);
    Route::put('/dashboard/profile', [ProfileController::class, 'update']);
    Route::post('/dashboard/profile/upload-foto', [ProfileController::class, 'uploadFoto']);
    Route::put('/dashboard/profile/password', [ProfileController::class, 'updatePassword']);

    // Reading sessions / last-read progress
    Route::get('/dashboard/sesi-baca', [SesiBacaController::class, 'index']);
    Route::get('/dashboard/sesi-baca/{materi}/last', [SesiBacaController::class, 'lastForMateri']);
    Route::post('/dashboard/sesi-baca', [SesiBacaController::class, 'store']);
    Route::post('/dashboard/sesi-baca/upsert', [SesiBacaController::class, 'upsert']);
    Route::put('/dashboard/sesi-baca/{id}', [SesiBacaController::class, 'update']);
    Route::delete('/dashboard/sesi-baca/{id}', [SesiBacaController::class, 'destroy']);

    // Rak Buku (bookshelf)
    Route::get('/dashboard/rak-buku', [RakBukuController::class, 'index']);
    Route::post('/dashboard/rak-buku', [RakBukuController::class, 'store']);
    Route::delete('/dashboard/rak-buku/{materi}', [RakBukuController::class, 'destroy']);
    Route::get('/dashboard/rak-buku/{materi}/status', [RakBukuController::class, 'status']);

    // Kuis (siswa)
    Route::get('/dashboard-siswa/kuis', [ApiKuisController::class, 'index']);
    Route::get('/dashboard-siswa/kuis/{kuis}', [ApiKuisController::class, 'show']);
    Route::post('/dashboard-siswa/kuis/{kuis}', [ApiKuisController::class, 'submitKuis']);
    Route::get('/dashboard-siswa/materi/{materi}/kuis', [ApiKuisController::class, 'showMateri']);
    Route::get('/dashboard-siswa/materi/{materi}/kuis/{kuis}', [ApiKuisController::class, 'showMateriKuis']);
    Route::post('/dashboard-siswa/materi/{materi}/kuis/{kuis}', [ApiKuisController::class, 'submitMateriKuis']);
    Route::get('/dashboard-siswa/riwayat/kuis', [ApiKuisController::class, 'riwayat']);
    Route::get('/dashboard-siswa/riwayat/kuis/{hasil}', [ApiKuisController::class, 'riwayatShow']);

    // Catatan siswa
    Route::get('/dashboard-siswa/catatan', [ApiCatatanController::class, 'index']);
    Route::post('/dashboard-siswa/catatan', [ApiCatatanController::class, 'store']);
    Route::delete('/dashboard-siswa/catatan/{catatan}', [ApiCatatanController::class, 'destroy']);

    
});
