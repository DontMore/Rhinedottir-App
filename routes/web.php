<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagementStockController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MsdsController;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\StorageLocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route User
Route::get('/', [AuthenticationController::class, 'login'])->name('login');
Route::get('/login', [AuthenticationController::class, 'login'])->name('login');
Route::post('/login', [AuthenticationController::class, 'authenticate']);
Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout')->middleware('auth');
Route::delete('/user/{id}', [AuthenticationController::class, 'deleteUser'])->name('user.delete')->middleware('admin');
// Toggle status user
Route::patch('/user/{id}/toggle-status', [AuthenticationController::class, 'toggleStatus'])->name('user.toggle-status');

// route user
Route::get('/user-list', [AuthenticationController::class, 'userList'])->middleware('admin')->name('user.index');
Route::get('/register', [AuthenticationController::class, 'register'])->middleware('admin')->name('user.register');
Route::get('/register-guest', [AuthenticationController::class, 'registerGuest'])->name('register.guest');
Route::post('/register', [AuthenticationController::class, 'store'])->name('user.store');
Route::get('/users/edit/{id}', [AuthenticationController::class, 'editUser'])->name('user.edit')->middleware('auth');
// Route::put('/users/update/{id}', [AuthenticationController::class, 'updateUser'])->name('user.update')->middleware('auth');
Route::put('/user/{id}', [AuthenticationController::class, 'update'])->name('user.update');
Route::post('forgot-password', [MailController::class, 'sendResetLink'])->name('forgot.password');


// route dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('admin')->name('dashboard.index');
Route::get('/chart-data', [DashboardController::class, 'getChartData']);
Route::get('/reagent-list', [DashboardController::class, 'getReagentList']);
Route::get('/reagent-chart', function () {
    return view('reagent_chart');
});
Route::get('/logbook-chart-data', [DashboardController::class, 'getLogbookChartData']);
Route::get('/logbook-reagent-list', [DashboardController::class, 'getLogbookReagentList']);
Route::get('/deviation-chart-data', [DashboardController::class, 'getDeviationData']);
Route::get('/batch-expiry-data', [DashboardController::class, 'getBatchExpiryData']);
Route::get('/logbook-chart', function () {
    return view('logbook_chart');
});

// route management stock
Route::get('/management-stock', [ManagementStockController::class, 'index'])->name('management-stock.index')->middleware('admin');
Route::get('/add-reagen', [ManagementStockController::class, 'addReagen'])->middleware('admin');
Route::post('/add-reagen', [ManagementStockController::class, 'addReagenStore'])
    ->name('management-stock.add-reagen.store') // ✅ Tambahkan nama route
    ->middleware('admin');
Route::get('/add-stock-reagen/{guid}', [ManagementStockController::class, 'addStockReagen'])->name('reagen.addstock')->middleware('admin');

Route::get('/view/{guid}', [ManagementStockController::class, 'viewReagen'])->name('data.view')->middleware('admin');
Route::get('/msds/{guid}', [ManagementStockController::class, 'showMsds'])->name('management-stock.msds')->middleware('admin');
Route::get('/coa/{id}', [ManagementStockController::class, 'showCoa'])->name('management-stock.coa')->middleware('admin');
Route::get('/edit/{guid}', [ManagementStockController::class, 'editReagen'])->name('data.edit')->middleware('admin');
Route::post('/delete/{guid}', [ManagementStockController::class, 'deleteReagen'])->name('data.delete')->middleware('admin');
Route::post('/update/{guid}', [ManagementStockController::class, 'updateReagen'])->name('data.update')->middleware('admin');
Route::put('/update/{guid}', [ManagementStockController::class, 'updateReagen'])->name('data.update')->middleware('admin');
Route::post('/reagen/{guid}', [ManagementStockController::class, 'getReagenData'])->middleware('admin');

Route::post('/add-stock-reagen', [ManagementStockController::class, 'addStock'])->name('reagen.addstockreagen')->middleware('admin');
Route::get('/generated-label/{id}', [ManagementStockController::class, 'generateLabel']);
Route::get('/generate-qr-code/{id}', [ManagementStockController::class, 'generateQrCode']);
Route::delete('/management-stock/delete-stock/{id}', [ManagementStockController::class, 'deleteStock'])->name('management-stock.delete-stock');
Route::get('/reagen-in', [ManagementStockController::class, 'reagenIn'])->name('data.reagenin');
Route::get('/reagen-out', [ManagementStockController::class, 'reagenOut'])->name('data.reagenout');
Route::get('/reagen-expired', [ManagementStockController::class, 'reagenExpired'])->name('reagen.expired')->middleware('admin');

Route::post('/group', [ManagementStockController::class, 'storeGroup'])->name('group.store');
Route::post('/category', [ManagementStockController::class, 'storeCategory'])->name('category.store');

// ✅ Multiple MSDS Reagen routes
Route::get('/reagen-msds/{id}/view', [ManagementStockController::class, 'viewReagenMsdsDocument'])
    ->name('reagen.msds.document.view')->middleware('admin');
Route::delete('/reagen-msds/{id}', [ManagementStockController::class, 'deleteReagenMsdsDocument'])
    ->name('reagen.msds.document.delete')->middleware('admin');

// ✅ Toggle Active/Inactive Reagen
Route::patch('/reagen/{guid}/toggle-status', [ManagementStockController::class, 'toggleReagenStatus'])
    ->name('reagen.toggle-status')
    ->middleware('admin');

// route logbook
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index'); // Route untuk menampilkan data logbook
Route::get('/take/{guid}', [LogbookController::class, 'takeReagen'])->name('data.take')->middleware('auth');
Route::get('/qrcode/{id}', [LogbookController::class, 'takeQRCode'])->name('qrcode')->middleware('auth')->middleware('auth');
Route::post('/take-process', [LogbookController::class, 'store'])->middleware('auth')->name('take.process')->middleware('auth');
Route::get('/logbook-history/{guid}', [LogbookController::class, 'logbookHistory'])->name('data.history');
Route::delete('/logbook-history/delete/{guid}', [LogbookController::class, 'deleteLogbookHistory'])
    ->middleware('admin')
    ->name('logbook-history.delete');
// Tambahkan rute ini jika belum ada
Route::get('/take-admin/{guid}', [LogbookController::class, 'takeAdmin'])->name('take-admin')->middleware('auth');
// Route untuk menyimpan data dari form
Route::post('/take-process-admin', [LogbookController::class, 'storeTakeAdmin'])->middleware('auth');


// route order
Route::middleware(['admin'])->group(function () {
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/new-order-form', [OrderController::class, 'newOrderForm'])->name('order.new');
    Route::get('/eksisting-order-form', [OrderController::class, 'eksistingOrderForm'])->name('order.eksisting');
    
    Route::post('/order', [OrderController::class, 'store'])->name('orders.store');
    
    // ✅ Semua route detail menggunakan {guid}
    Route::get('/view-order/{guid}', [OrderController::class, 'viewOrder'])->name('order.view');
    Route::post('/update-order/{guid}', [OrderController::class, 'update'])->name('order.update');
    Route::delete('/order-delete/{guid}', [OrderController::class, 'destroy'])->name('order.delete');
    
    // ✅ AJAX endpoint juga menggunakan GUID
    Route::get('/reagen/{guid}', [OrderController::class, 'getReagenData']);
});


 // route report
Route::get('/report', [ReportController::class, 'index'])->middleware('admin')->name('report.index');
Route::get('/report/reagen-monthly', [ReportController::class, 'reagenMonthlyReport'])->middleware('admin')->name('report.reagen-monthly');
Route::get('/report/reagen-monthly-excel', [ReportController::class, 'exportReagenMonthlyExcel'])->middleware('admin')->name('report.reagen-monthly-excel');
Route::get('/report/reagen-monthly-pdf', [ReportController::class, 'generateReagenMonthlyPDF'])->middleware('admin')->name('report.reagen-monthly-pdf');
Route::get('/report-detail', [ReportController::class, 'reportDetail'])->name('reportDetail');
Route::get('/generate-pdf', [ReportController::class, 'generatePDF'])->name('report.print')->middleware('admin');
Route::post('/filter-logbook', [ReportController::class, 'filterLogbook'])->name('report.filter');
Route::get('/export-logbook-pdf', [ReportController::class, 'exportLogbookPDF'])->name('report.export-logbook');
Route::get('/generate-logbook-pdf', [ReportController::class, 'generateLogbookPDF'])->name('report.logbook-pdf');
Route::get('/export-logbook-excel', [ReportController::class, 'exportExcel'])->name('report.logbook-excel');
Route::get('/historical-report', [ReportController::class, 'historicalReport'])->name('report.historical');
Route::get('/reagen-list', [ReportController::class, 'reagenList'])->name('report.reagen-list');
Route::get('/report/reagen-annual-usage', [ReportController::class, 'annualReagenUsageReport'])->middleware('admin')->name('report.reagen-annual-usage');
Route::get('/historical-pdf', [ReportController::class, 'generateHistoricalPDF'])->name('report.historical-pdf');
Route::get('/stock-opname-report', [ReportController::class, 'stockOpnameReport'])->name('report.stock-opname');
Route::get('/stock-opname-pdf', [ReportController::class, 'generateStockOpnamePDF'])->name('report.stock-opname-pdf');
Route::get('/stock-opname-excel', [ReportController::class, 'exportStockOpnameExcel'])->name('report.stock-opname-excel');
Route::get('/expired-reagen', [ReportController::class, 'expiredReagen'])->name('report.expired-reagen');
Route::get('/expired-reagen-pdf', [ReportController::class, 'exportExpiredPDF'])->name('report.expired-pdf');
Route::get('/expired-reagen-excel', [ReportController::class, 'exportExpiredExcel'])->name('report.expired-excel');
Route::get('/reagen-list-pdf', [ReportController::class, 'generateReagenListPDF'])->name('report.reagen-pdf');


// route stock opname
Route::get('/stock-opname', [StockOpnameController::class, 'index'])->middleware('admin')->name('stock.index');
Route::post('/update-quantities', [StockOpnameController::class, 'updateQuantities'])->name('update.quantities')->middleware('admin');
Route::get('/generate-pdf-stock', [StockOpnameController::class, 'generateStock'])->name('stock.print')->middleware('admin');
Route::get('/get-reagen/{id}', [StockOpnameController::class, 'getReagen']);
Route::post('/stock/update', [StockOpnameController::class, 'update'])->name('stock.update');
Route::get('/generate-data', [StockOpnameController::class, 'generate'])->name('generateData');
Route::get('/so-detail', [StockOpnameController::class, 'soDetail'])->name('soDetail');
Route::post('/generated-so', [StockOpnameController::class, 'generatedSO'])->name('generatedSO');
Route::get('/stock-opname/stock-adjustment', [StockOpnameController::class, 'stockAdjustment'])->middleware('admin')->name('stock.stock-adjustment');
Route::post('/stock-opname/stock-adjustment/preview', [StockOpnameController::class, 'previewStockAdjustment'])->middleware('admin')->name('stock.stock-adjustment.preview');
Route::post('/stock-opname/stock-adjustment/process', [StockOpnameController::class, 'processStockAdjustment'])->middleware('admin')->name('stock.stock-adjustment.process');
Route::get('/api/stock-history-summary', [StockOpnameController::class, 'getStockHistorySummary'])->middleware('admin')->name('stock.history-summary');

//route email
Route::get('kirim-email', 'App\Http\Controllers\MailController@index');
Route::get('/reset-password', [MailController::class, 'resetPassword'])->name('password.reset');
Route::get('/reset-password/{token}', [MailController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password',  [MailController::class, 'update'])->middleware('guest')->name('password.update');

use App\Http\Controllers\BackupController;

// Settings Routes
Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    // Shared Routes (All Roles)
    Route::get('/profile', [SettingsController::class, 'profileSettings'])->name('profile');
    Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
    
    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/', [SettingsController::class, 'emailSettings'])->name('index');
        Route::get('/email', [SettingsController::class, 'emailSettings'])->name('email');
        Route::post('/email', [SettingsController::class, 'updateEmailSettings'])->name('email.update');
        Route::get('/api', [SettingsController::class, 'apiSettings'])->name('api');
        Route::post('/api', [SettingsController::class, 'updateApiSettings'])->name('api.update');
        Route::post('/api/regenerate-token', [SettingsController::class, 'regenerateApiToken'])->name('api.regenerate-token');
        
        // Backup Routes
        Route::get('/backup', [BackupController::class, 'index'])->name('backup');
        Route::post('/backup/download', [BackupController::class, 'downloadBackup'])->name('backup.download');

        // ✅ SCHEDULER ROUTES (Pindahkan ke sini)
        Route::get('/scheduler', [SettingsController::class, 'schedulerIndex'])->name('scheduler');
        Route::post('/scheduler/start', [SettingsController::class, 'startScheduler'])->name('scheduler.start');
        Route::post('/scheduler/stop',  [SettingsController::class, 'stopScheduler'])->name('scheduler.stop');
        Route::get('/scheduler/status', [SettingsController::class, 'getSchedulerStatus'])->name('scheduler.status');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin', [SuperAdminController::class, 'index'])->name('superadmin.index')->middleware('admin');
    Route::get('/organization/create', [OrganizationController::class, 'create'])->name('organization.create')->middleware('admin');
    Route::post('/organization', [OrganizationController::class, 'store'])->name('organization.store')->middleware('admin');
    Route::get('/organization/{id}/edit', [OrganizationController::class, 'edit'])->name('organization.edit')->middleware('admin');
    Route::put('/organization/{id}', [OrganizationController::class, 'update'])->name('organization.update')->middleware('admin');
    Route::delete('/organization/{id}', [OrganizationController::class, 'destroy'])->name('organization.destroy')->middleware('admin');
    Route::get('/organization/{id}/settings', [OrganizationController::class, 'settings'])->name('organization.settings')->middleware('admin');
    Route::get('/organization/{id}/add-user', [OrganizationController::class, 'addUser'])->name('organization.adduser')->middleware('admin');
    Route::post('/organization/{id}/add-user', [OrganizationController::class, 'storeUser'])->name('organization.storeuser')->middleware('admin');
    Route::get('/organization/{id}', [OrganizationController::class, 'show'])->name('organization.show')->middleware('admin');
});

// routes/web.php
Route::get('/test-smtp', function () {
    \App\Models\EmailSetting::updateConfig();

    try {
        \Illuminate\Support\Facades\Mail::raw('Test connection', function ($msg) {
            $msg->to('developer@onexternal.com')->subject('SMTP Test');
        });
        return '✅ Email terkirim!';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});

use App\Http\Controllers\AuditLogController;

// ⚠️ WAJIB: Bungkus dengan middleware auth/admin agar tidak diakses publik
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

Route::middleware(['admin'])->group(function () {
    // ... route existing ...
    Route::get('/storage-location', [StorageLocationController::class, 'index'])->name('storage.index');
    Route::post('/storage-location', [StorageLocationController::class, 'store'])->name('storage.store');
    Route::put('/storage-location/{guid}', [StorageLocationController::class, 'update'])->name('storage.update');
Route::delete('/storage-location/{guid}', [StorageLocationController::class, 'destroy'])->name('storage.delete');
});

use App\Http\Controllers\SsoController;

// Taruh DI LUAR middleware auth (agar bisa diakses saat belum login)
Route::get('/sso/login', [SsoController::class, 'login'])->name('sso.login');
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');

Route::prefix('msds')->name('msds.')->middleware(['auth'])->group(function () {
    Route::get('/', [MsdsController::class, 'index'])->name('index');
    
    // ✅ BARU: Route untuk melihat semua materi training milik 1 reagen
    Route::get('/manage/{reagen}', [MsdsController::class, 'manage'])->name('manage');
    
    Route::get('/upload/{reagen}', [MsdsController::class, 'create'])->name('create');
    Route::post('/store', [MsdsController::class, 'store'])->name('store');
    Route::get('/view/{document}', [MsdsController::class, 'show'])->name('show'); // Ubah parameter ke document ID
    Route::delete('/document/{document}', [MsdsController::class, 'destroy'])->name('destroy'); // Hapus per file
});