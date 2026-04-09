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
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\OrganizationController;

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
Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout')->middleware('admin');
Route::delete('/user/{id}', [AuthenticationController::class, 'deleteUser'])->name('user.delete')->middleware('admin');

// route user
Route::get('/user-list', [AuthenticationController::class, 'userList'])->middleware('admin')->name('user.index');
Route::get('/register', [AuthenticationController::class, 'register'])->middleware('admin')->name('user.register');
Route::get('/register-guest', [AuthenticationController::class, 'registerGuest'])->name('register.guest');
Route::post('/register', [AuthenticationController::class, 'store']);
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
Route::get('/logbook-chart', function () {
    return view('logbook_chart');
});

// route management stock
Route::get('/management-stock', [ManagementStockController::class, 'index'])->name('management-stock.index')->middleware('admin');
Route::get('/add-reagen', [ManagementStockController::class, 'addReagen'])->middleware('admin');
Route::post('/add-reagen', [ManagementStockController::class, 'addReagenStore'])->middleware('admin');
Route::get('/add-stock-reagen/{noCatalog}', [ManagementStockController::class, 'addStockReagen'])->name('reagen.addstock')->middleware('admin');
Route::get('/view/{noCatalog}', [ManagementStockController::class, 'viewReagen'])->name('data.view')->middleware('admin');
Route::get('/edit/{noCatalog}', [ManagementStockController::class, 'editReagen'])->name('data.edit')->middleware('admin');
Route::post('/delete/{noCatalog}', [ManagementStockController::class, 'deleteReagen'])->name('data.delete')->middleware('admin');
Route::post('/update/{noCatalog}', [ManagementStockController::class, 'updateReagen'])->name('data.update')->middleware('admin');
Route::post('/reagen/{noCatalog}', [ManagementStockController::class, 'getReagenData'])->middleware('admin');
Route::post('/add-stock-reagen', [ManagementStockController::class, 'addStock'])->name('reagen.addstockreagen')->middleware('admin');
Route::get('/generated-label/{id}', [ManagementStockController::class, 'generateLabel']);
Route::get('/generate-qr-code/{id}', [ManagementStockController::class, 'generateQrCode']);
Route::delete('/management-stock/delete-stock/{id}', [ManagementStockController::class, 'deleteStock'])->name('management-stock.delete-stock');
Route::get('/reagen-in', [ManagementStockController::class, 'reagenIn'])->name('data.reagenin');
Route::get('/reagen-out', [ManagementStockController::class, 'reagenOut'])->name('data.reagenout');
Route::get('/reagen-expired', [ManagementStockController::class, 'reagenExpired'])->name('reagen.expired')->middleware('admin');

// route logbook
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index'); // Route untuk menampilkan data logbook
Route::get('/take/{noCatalog}', [LogbookController::class, 'takeReagen'])->name('data.take')->middleware('auth');
Route::get('/qrcode/{id}', [LogbookController::class, 'takeQRCode'])->name('qrcode')->middleware('auth')->middleware('auth');
Route::post('/take-process', [LogbookController::class, 'store'])->middleware('auth')->name('take.process')->middleware('auth');
Route::get('/logbook-history/{noCatalog}', [LogbookController::class, 'logbookHistory'])->name('data.history');
// Tambahkan rute ini jika belum ada
Route::get('/take-admin/{noCatalog}', [LogbookController::class, 'takeAdmin'])->name('take-admin')->middleware('auth');
// Route untuk menyimpan data dari form
Route::post('/take-process-admin', [LogbookController::class, 'storeTakeAdmin'])->middleware('auth');


// route order
Route::get('/order', [OrderController::class, 'index'])->name('order.index')->middleware('admin');
Route::get('/new-order-form', [OrderController::class, 'newOrderForm'])->middleware('admin');
Route::get('/eksisting-order-form', [OrderController::class, 'EksistingOrderForm'])->middleware('admin');
Route::post('/order', [OrderController::class, 'store'])->middleware('admin')->name('orders.store');
Route::get('/view-order/{id}', [OrderController::class, 'viewOrder'])->middleware('admin')->name('order.view');
Route::post('/update-order/{id}', [OrderController::class, 'update'])->middleware('admin')->name('order.update');
Route::delete('/order-delete/{id}', [OrderController::class, 'destroy'])->middleware('admin')->name('order.delete');
Route::get('/reagen/{noCatalogUtama}', [OrderController::class, 'getReagenData'])->middleware('admin');


// route report
Route::get('/report', [ReportController::class, 'index'])->middleware('admin')->name('report.index');
Route::get('/report-detail', [ReportController::class, 'reportDetail'])->name('reportDetail');
Route::get('/generate-pdf', [ReportController::class, 'generatePDF'])->name('report.print')->middleware('admin');
Route::post('/filter-logbook', [ReportController::class, 'filterLogbook'])->name('report.filter');
Route::get('/export-logbook-pdf', [ReportController::class, 'exportLogbookPDF'])->name('report.export-logbook');
Route::get('/generate-logbook-pdf', [ReportController::class, 'generateLogbookPDF'])->name('report.logbook-pdf');
Route::get('/export-logbook-excel', [ReportController::class, 'exportExcel'])->name('report.logbook-excel');
Route::get('/historical-report', [ReportController::class, 'historicalReport'])->name('report.historical');
Route::get('/reagen-list', [ReportController::class, 'reagenList'])->name('report.reagen-list');
Route::get('/historical-pdf', [ReportController::class, 'generateHistoricalPDF'])->name('report.historical-pdf');
Route::get('/historical-excel', [ReportController::class, 'exportHistoricalExcel'])->name('report.historical-excel');
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

//route email
Route::get('kirim-email','App\Http\Controllers\MailController@index');
Route::get('/reset-password', [MailController::class, 'resetPassword'])->name('password.reset');
Route::get('/reset-password/{token}', [MailController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password',  [MailController::class, 'update'])->middleware('guest')->name('password.update');

// Settings Routes
Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/email', [SettingsController::class, 'emailSettings'])->name('email');
    Route::post('/email', [SettingsController::class, 'updateEmailSettings'])->name('email.update');
    Route::get('/profile', [SettingsController::class, 'profileSettings'])->name('profile');
    Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
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
