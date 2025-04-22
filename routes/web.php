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
Route::get('/register', [AuthenticationController::class, 'register'])->middleware('admin');;
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

// route logbook
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index')->middleware('auth'); // Route untuk menampilkan data logbook
Route::get('/take/{noCatalog}', [LogbookController::class, 'takeReagen'])->name('data.take')->middleware('auth');
Route::get('/qrcode/{id}', [LogbookController::class, 'takeQRCode'])->name('qrcode')->middleware('auth');
Route::post('/take-process', [LogbookController::class, 'store'])->middleware('auth');
Route::get('/logbook-history/{noCatalog}', [LogbookController::class, 'logbookHistory'])->name('data.history')->middleware('auth');
// Tambahkan rute ini jika belum ada
Route::get('/take-admin/{noCatalog}', [LogbookController::class, 'takeAdmin'])->name('take-admin');
// Route untuk menyimpan data dari form
Route::post('/take-process-admin', [LogbookController::class, 'storeTakeAdmin']);


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