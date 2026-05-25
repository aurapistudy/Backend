<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PanduanController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing.home');
Route::post('/ulasan', [LandingPageController::class, 'storeUlasan'])->name('landing.ulasan.store');
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media.public.show');

// Debug route to log sidebar clicks (enabled only in debug mode).
if (config('app.debug')) {
    Route::get('/debug/nav-log', function (Request $request) {
        Log::info('nav_click_debug', [
            'current_url' => $request->query('current_url'),
            'target_url' => $request->query('target_url'),
            'text' => $request->query('text'),
            'timestamp' => $request->query('ts'),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return response()->noContent();
    });
}

// Auth Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);

// Dashboard Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard-siswa', [App\Http\Controllers\SiswaDashboardController::class, 'index'])
        ->name('dashboard.siswa');
    Route::get('/dashboard-siswa/materi', [App\Http\Controllers\SiswaDashboardController::class, 'materi'])
        ->name('dashboard.siswa.materi');
    Route::get('/dashboard-siswa/materi/{materi}', [App\Http\Controllers\SiswaDashboardController::class, 'showMateri'])
        ->name('dashboard.siswa.materi.show');
    Route::get('/dashboard-siswa/materi/{materi}/baca', [App\Http\Controllers\SiswaDashboardController::class, 'readMateri'])
        ->name('dashboard.siswa.materi.read');
    Route::get('/dashboard-siswa/materi/{materi}/kuis', [App\Http\Controllers\SiswaDashboardController::class, 'kuisMateri'])
        ->name('dashboard.siswa.materi.kuis');
    Route::get('/dashboard-siswa/materi/{materi}/kuis/{kuis}', [App\Http\Controllers\SiswaDashboardController::class, 'kuisMateriShow'])
        ->name('dashboard.siswa.materi.kuis.show');
    Route::post('/dashboard-siswa/materi/{materi}/kuis/{kuis}', [App\Http\Controllers\SiswaDashboardController::class, 'submitKuisMateri'])
        ->name('dashboard.siswa.materi.kuis.submit');
    Route::get('/dashboard-siswa/kuis', [App\Http\Controllers\SiswaDashboardController::class, 'kuisIndex'])
        ->name('dashboard.siswa.kuis');
    Route::get('/dashboard-siswa/kuis/{kuis}', [App\Http\Controllers\SiswaDashboardController::class, 'kuisUmumShow'])
        ->name('dashboard.siswa.kuis.show');
    Route::post('/dashboard-siswa/kuis/{kuis}', [App\Http\Controllers\SiswaDashboardController::class, 'submitKuisUmum'])
        ->name('dashboard.siswa.kuis.submit');
    Route::get('/dashboard-siswa/catatan', [App\Http\Controllers\SiswaDashboardController::class, 'catatan'])
        ->name('dashboard.siswa.catatan');
    Route::post('/dashboard-siswa/catatan', [App\Http\Controllers\SiswaDashboardController::class, 'storeCatatan'])
        ->name('dashboard.siswa.catatan.store');
    Route::delete('/dashboard-siswa/catatan/{catatan}', [App\Http\Controllers\SiswaDashboardController::class, 'destroyCatatan'])
        ->name('dashboard.siswa.catatan.destroy');
    Route::get('/dashboard-siswa/riwayat', [App\Http\Controllers\SiswaDashboardController::class, 'riwayat'])
        ->name('dashboard.siswa.riwayat');
    Route::get('/dashboard-siswa/riwayat/kuis/{hasil}', [App\Http\Controllers\SiswaDashboardController::class, 'riwayatKuisShow'])
        ->name('dashboard.siswa.riwayat.kuis.show');
    Route::get('/dashboard-siswa/perintah-suara', [App\Http\Controllers\SiswaDashboardController::class, 'perintahSuara'])
        ->name('dashboard.siswa.perintah-suara');
    Route::get('/dashboard-siswa/pengaturan', [App\Http\Controllers\SiswaDashboardController::class, 'pengaturan'])
        ->name('dashboard.siswa.pengaturan');
    Route::post('/dashboard-siswa/pengaturan', [App\Http\Controllers\SiswaDashboardController::class, 'updatePengaturan'])
        ->name('dashboard.siswa.pengaturan.update');
    Route::post('/dashboard-siswa/kelas', [App\Http\Controllers\SiswaDashboardController::class, 'updateKelas'])
        ->name('dashboard.siswa.kelas.update');
    Route::get('/dashboard-siswa/panduan', [App\Http\Controllers\SiswaDashboardController::class, 'panduan'])
        ->name('dashboard.siswa.panduan');
    Route::get('/dashboard-siswa/rak-buku', [App\Http\Controllers\SiswaDashboardController::class, 'rakBuku'])
        ->name('dashboard.siswa.rak-buku');
    Route::post('/dashboard-siswa/rak-buku', [App\Http\Controllers\SiswaDashboardController::class, 'addRakBuku'])
        ->name('dashboard.siswa.rak-buku.add');
    Route::delete('/dashboard-siswa/rak-buku/{materiId}', [App\Http\Controllers\SiswaDashboardController::class, 'removeRakBuku'])
        ->name('dashboard.siswa.rak-buku.remove');

    Route::middleware('staff')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

        Route::get('dashboard/mata-pelajaran', [App\Http\Controllers\MataPelajaranController::class, 'index'])
            ->name('mata-pelajaran.index');
        Route::get('dashboard/mata-pelajaran/{mata_pelajaran}', [App\Http\Controllers\MataPelajaranController::class, 'show'])
            ->name('mata-pelajaran.show');

        Route::resource('dashboard/materi', App\Http\Controllers\MateriController::class)->names([
        'index' => 'materi.index',
        'create' => 'materi.create',
        'store' => 'materi.store',
        'show' => 'materi.show',
        'edit' => 'materi.edit',
        'update' => 'materi.update',
        'destroy' => 'materi.destroy',
    ]);
    Route::post('dashboard/materi/cover-preview/generate', [App\Http\Controllers\MateriController::class, 'generateCoverPreview'])
        ->name('materi.generate-cover-preview');
    Route::delete('dashboard/materi/cover-preview/discard', [App\Http\Controllers\MateriController::class, 'discardCoverPreview'])
        ->name('materi.discard-cover-preview');
    Route::get('dashboard/materi/{materi}/bab/create', [App\Http\Controllers\MateriBabController::class, 'create'])
        ->name('materi.bab.create');
    Route::post('dashboard/materi/{materi}/bab', [App\Http\Controllers\MateriBabController::class, 'store'])
        ->name('materi.bab.store');
    Route::get('dashboard/materi/{materi}/bab/{bab}/edit', [App\Http\Controllers\MateriBabController::class, 'edit'])
        ->name('materi.bab.edit');
    Route::put('dashboard/materi/{materi}/bab/{bab}', [App\Http\Controllers\MateriBabController::class, 'update'])
        ->name('materi.bab.update');
    Route::post('dashboard/materi/{materi}/bab/{bab}/generate-summary', [App\Http\Controllers\MateriBabController::class, 'generateSummary'])
        ->name('materi.bab.generate-summary');
    Route::delete('dashboard/materi/{materi}/bab/{bab}', [App\Http\Controllers\MateriBabController::class, 'destroy'])
        ->name('materi.bab.destroy');

        Route::resource('dashboard/kuis', App\Http\Controllers\KuisController::class)->names([
        'index' => 'kuis.index',
        'create' => 'kuis.create',
        'store' => 'kuis.store',
        'show' => 'kuis.show',
        'edit' => 'kuis.edit',
        'update' => 'kuis.update',
        'destroy' => 'kuis.destroy',
    ]);
    Route::post('dashboard/kuis/generate-from-materi', [App\Http\Controllers\KuisController::class, 'generateFromMateri'])
        ->name('kuis.generate-from-materi');
    Route::get('dashboard/kuis-hasil', [App\Http\Controllers\KuisController::class, 'hasilIndex'])
        ->name('kuis.hasil.index');
    Route::get('dashboard/kuis-hasil/siswa/{pengguna}', [App\Http\Controllers\KuisController::class, 'hasilSiswa'])
        ->name('kuis.hasil.siswa');
    Route::get('dashboard/kuis-hasil/{hasil}', [App\Http\Controllers\KuisController::class, 'hasilShow'])
        ->name('kuis.hasil.show');
        Route::post('dashboard/kuis-hasil/{hasil}', [App\Http\Controllers\KuisController::class, 'hasilUpdate'])
            ->name('kuis.hasil.update');

        Route::get('/dashboard/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
        Route::put('/dashboard/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/dashboard/profile/upload-foto', [App\Http\Controllers\ProfileController::class, 'uploadFoto'])->name('profile.upload-foto');
        Route::put('/dashboard/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::middleware('admin')->group(function () {
            Route::resource('dashboard/panduan', PanduanController::class)->names([
                'index' => 'panduan.index',
                'create' => 'panduan.create',
                'store' => 'panduan.store',
                'show' => 'panduan.show',
                'edit' => 'panduan.edit',
                'update' => 'panduan.update',
                'destroy' => 'panduan.destroy',
            ]);

            Route::resource('dashboard/fiksi', App\Http\Controllers\FiksiController::class)->names([
                'index' => 'fiksi.index',
                'create' => 'fiksi.create',
                'store' => 'fiksi.store',
                'show' => 'fiksi.show',
                'edit' => 'fiksi.edit',
                'update' => 'fiksi.update',
                'destroy' => 'fiksi.destroy',
            ]);

            Route::resource('dashboard/aac', App\Http\Controllers\AacController::class)->names([
                'index' => 'aac.index',
                'create' => 'aac.create',
                'store' => 'aac.store',
                'show' => 'aac.show',
                'edit' => 'aac.edit',
                'update' => 'aac.update',
                'destroy' => 'aac.destroy',
            ]);

            Route::resource('dashboard/pengguna', App\Http\Controllers\PenggunaController::class)->names([
                'index' => 'pengguna.index',
                'create' => 'pengguna.create',
                'store' => 'pengguna.store',
                'show' => 'pengguna.show',
                'edit' => 'pengguna.edit',
                'update' => 'pengguna.update',
                'destroy' => 'pengguna.destroy',
            ]);

            Route::resource('dashboard/level', App\Http\Controllers\LevelController::class)->names([
                'index' => 'level.index',
                'create' => 'level.create',
                'store' => 'level.store',
                'show' => 'level.show',
                'edit' => 'level.edit',
                'update' => 'level.update',
                'destroy' => 'level.destroy',
            ]);

            Route::resource('dashboard/mata-pelajaran', App\Http\Controllers\MataPelajaranController::class)
                ->except(['index', 'show'])
                ->names([
                    'create' => 'mata-pelajaran.create',
                    'store' => 'mata-pelajaran.store',
                    'edit' => 'mata-pelajaran.edit',
                    'update' => 'mata-pelajaran.update',
                    'destroy' => 'mata-pelajaran.destroy',
                ]);

            Route::resource('dashboard/landing', App\Http\Controllers\LandingItemController::class)->names([
                'index' => 'landing.index',
                'create' => 'landing.create',
                'store' => 'landing.store',
                'show' => 'landing.show',
                'edit' => 'landing.edit',
                'update' => 'landing.update',
                'destroy' => 'landing.destroy',
            ]);

            Route::get('/dashboard/ulasan', [App\Http\Controllers\UlasanController::class, 'index'])
                ->name('ulasan.index');
            Route::get('/dashboard/ulasan/export', [App\Http\Controllers\UlasanController::class, 'exportCsv'])
                ->name('ulasan.export');
            Route::delete('/dashboard/ulasan/{ulasan}', [App\Http\Controllers\UlasanController::class, 'destroy'])
                ->name('ulasan.destroy');
        });
    });
});
