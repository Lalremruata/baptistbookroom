<?php

use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\PrivateBookPaymentsController;
use App\Http\Controllers\PrivateBookReturnsController;
use App\Http\Controllers\SalesInvoicesController;
use Illuminate\Support\Facades\Route;

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

// Route::redirect('/', '/login');
Route::get('/receipt/download',[InvoicesController::class, 'downloadInvoice'])
->name('stockdistribute.receipt.download');
Route::get('/sale/receipt/download',[SalesInvoicesController::class, 'downloadInvoice'])
->name('sale.receipt.download');
Route::get('{privateBookAccount}/private-book-payment/receipt/download',[PrivateBookPaymentsController::class, 'downloadInvoice'])
->name('private-book-payment.receipt.download');
Route::get('{privateBookReturn}/private-book-return/receipt/download',[PrivateBookReturnsController::class, 'downloadInvoice'])
->name('private-book-return.receipt.download');
