<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PenerbitController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\UmumController;
use App\Http\Controllers\UserController;
use App\Mail\HelloMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/email', function () {
    try {
        Mail::to('noerfaris@gmail.com')->send(new HelloMail());
        return 'berhasil';
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
});

Route::middleware('xss')->group(function () {

    Route::get('/scanner/{slug}', [ScannerController::class, 'tampilan'])->name('scanner.frontend');
    Route::post('/scanner/input', [ScannerController::class, 'checkin'])->name('scanner.checkin');

    Route::any('/', [LoginController::class, 'index'])->name('login');

    Route::middleware(['auth'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
            Route::any('/profil', [UserController::class, 'profil'])->name('profil');
            Route::post('/simpan-foto', [UserController::class, 'simpan_foto'])->name('simpan-foto');

            Route::any('/password', [UserController::class, 'password'])->name('password');
            Route::post('/ganti-password', [UserController::class, 'ganti_password'])->name('ganti-password');
            Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
            Route::any('/weblog', [LogController::class, 'index'])->name('aktivitas');
            Route::get('/force-login/{id}', [LoginController::class, 'force_login'])->name('force-login');

            Route::post('/chart-sel', [HomeController::class, 'chart_sel'])->name('chart-sel');
            Route::post('/chart-sel-napi', [HomeController::class, 'chart_sel_napi'])->name('chart-sel-napi');

            // Data Master
            Route::resource('siswa', AnggotaController::class);
            Route::get('/siswa/kartu/{anggota}', [AnggotaController::class, 'kartu'])->name('siswa.kartu');
            Route::post('/ajax-siswa', [AnggotaController::class, 'ajax'])->name('ajax-siswa');
            Route::post('/siswa-ganti-password', [AnggotaController::class, 'ganti_password'])->name('siswa-ganti-password');

            Route::resource('kelas', KelasController::class);
            Route::post('/ajax-kelas', [KelasController::class, 'ajax'])->name('ajax-kelas');

            Route::resource('scanner', ScannerController::class);
            Route::post('/ajax-scanner', [ScannerController::class, 'ajax'])->name('ajax-scanner');

            // Pengaturan
            Route::singleton('umum', UmumController::class);
            Route::get('/umum/peminjaman', [UmumController::class, 'peminjaman'])->name('umum.peminjaman');
            Route::any('/umum/peminjaman/edit', [UmumController::class, 'editPinjam'])->name('umum.peminjaman.edit');

            Route::resource('user', UserController::class);
            Route::post('/ajax-user', [UserController::class, 'ajax'])->name('ajax-user');

            Route::resource('banner', BannerController::class);
            Route::post('/ajax-banner', [BannerController::class, 'ajax'])->name('ajax-banner');

            Route::resource('role', RoleController::class);
            Route::post('/ajax-role', [RoleController::class, 'ajax'])->name('ajax-roles');
            Route::resource('permission', PermissionController::class);
            Route::post('/ajax-permission', [PermissionController::class, 'ajax'])->name('ajax-permission');

            // ajax
            Route::prefix('ajax')->group(function () {
                Route::post('/role', [AjaxController::class, 'role'])->name('drop-role');
                Route::post('/provinsi', [AjaxController::class, 'provinsi'])->name('drop-provinsi');
                Route::post('/kota', [AjaxController::class, 'kota'])->name('drop-kota');
                Route::post('/kecamatan', [AjaxController::class, 'kecamatan'])->name('drop-kecamatan');
                Route::post('/kelas', [AjaxController::class, 'kelas'])->name('drop-kelas');
                Route::post('/kategori', [AjaxController::class, 'kategori'])->name('drop-kategori');
                Route::post('/penerbit', [AjaxController::class, 'penerbit'])->name('drop-penerbit');
                Route::post('/ganti-foto', [AjaxController::class, 'ganti_foto'])->name('ganti-foto');
                Route::post('/ganti-pdf', [AjaxController::class, 'ganti_pdf'])->name('ganti-pdf');
            });
        });
    });
});
