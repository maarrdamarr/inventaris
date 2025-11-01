<?php

use App\Http\Controllers\CommodityAcquisitionController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\CommodityLocationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
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
Route::get('/laporan', function () {
return view('reports.index', [
'title' => 'Laporan',
'page_heading' => 'Laporan',
]);
})->name('laporan.index')->middleware('auth');

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('barang', CommodityController::class)->except('create', 'edit', 'show')->parameter('barang', 'commodity');
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::post('/print', [CommodityController::class, 'generatePDF'])->name('print');
        Route::post('/print/{id}', [CommodityController::class, 'generatePDFIndividually'])->name('print-individual');
        Route::post('/export', [CommodityController::class, 'export'])->name('export');
        Route::post('/import', [CommodityController::class, 'import'])->name('import');
    });

    Route::resource('perolehan', CommodityAcquisitionController::class)
        ->except('create', 'edit', 'show')
        ->parameter('perolehan', 'commodity_acquisition');

    Route::resource('ruangan', CommodityLocationController::class)->except('create', 'edit', 'show')
        ->parameter('ruangan', 'commodity_location');
    Route::post('/ruangan/import', [CommodityLocationController::class, 'import'])->name('ruangan.import');
    Route::post('/ruangan/export', [CommodityLocationController::class, 'export'])->name('ruangan.export');

    Route::resource('pengguna', UserController::class)->except('create', 'edit', 'show')
        ->parameter('pengguna', 'user');

    Route::resource('peran-dan-hak-akses', RoleController::class)->parameter('peran-dan-hak-akses', 'role');

    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/peminjaman', [BorrowingController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [BorrowingController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/kelola', [BorrowingController::class, 'manageIndex'])->name('peminjaman.manage')->middleware('permission:kelola peminjaman');
    Route::get('/peminjaman/riwayat', [BorrowingController::class, 'myIndex'])->name('peminjaman.my');
    Route::post('/peminjaman/{borrowing}/approve', [BorrowingController::class, 'approve'])->name('peminjaman.approve')->middleware('permission:kelola peminjaman');
    Route::post('/peminjaman/{borrowing}/reject', [BorrowingController::class, 'reject'])->name('peminjaman.reject')->middleware('permission:kelola peminjaman');
    Route::post('/peminjaman/{borrowing}/returned', [BorrowingController::class, 'returned'])->name('peminjaman.returned')->middleware('permission:kelola peminjaman');

    // Kerusakan
    Route::get('/kerusakan', [DamageReportController::class, 'index'])->name('kerusakan.index')->middleware('permission:kelola kerusakan');
    Route::get('/kerusakan/baru', [DamageReportController::class, 'create'])->name('kerusakan.create')->middleware('permission:lapor kerusakan');
    Route::post('/kerusakan', [DamageReportController::class, 'store'])->name('kerusakan.store')->middleware('permission:lapor kerusakan');
    Route::get('/kerusakan/{damage}', [DamageReportController::class, 'show'])->name('kerusakan.show');
    Route::post('/kerusakan/{damage}/start', [DamageReportController::class, 'start'])->name('kerusakan.start')->middleware('permission:kelola kerusakan');
    Route::post('/kerusakan/{damage}/resolve', [DamageReportController::class, 'resolve'])->name('kerusakan.resolve')->middleware('permission:kelola kerusakan');
    Route::post('/kerusakan/{damage}/comment', [DamageReportController::class, 'comment'])->name('kerusakan.comment');
    Route::post('/kerusakan/{damage}/evidence', [DamageReportController::class, 'addEvidence'])->name('kerusakan.evidence.add')->middleware('permission:kelola kerusakan');
    Route::delete('/kerusakan/evidence/{file}', [DamageReportController::class, 'deleteEvidence'])->name('kerusakan.evidence.delete')->middleware('permission:kelola kerusakan');

    Route::get('/kerusakan-export/excel', [DamageReportController::class, 'exportExcel'])->name('kerusakan.export.excel')->middleware('permission:kelola kerusakan');
    Route::get('/kerusakan-export/pdf', [DamageReportController::class, 'exportPdf'])->name('kerusakan.export.pdf')->middleware('permission:kelola kerusakan');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/open/{id}', [NotificationController::class, 'open'])->name('notifications.open');
});
