<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VesselsController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FixedFeeController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// مسارات تقرير السفينة
Route::get('/pdf/vesselReport/{id}/{clickOption?}', [VesselsController::class, 'generateSailingReportPdf'])
    ->name('pdf.vesselReport.vessel_report')
    ->middleware(['auth']);

// مسارات فاتورة بروفورما
Route::get('/pdf/proformaInvoice/{id}/{clickOption?}', [VesselsController::class, 'generateProformaInvoicePdf'])
    ->name('pdf.proformaInvoice.proforma_invoice')
    ->middleware(['auth']);


Route::resource('vessels', VesselsController::class)->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// مسارات فئات الرسوم
Route::resource('fee_categories', FeeCategoryController::class);

// مسارات الرسوم الثابتة
Route::resource('fixed_fees', FixedFeeController::class);

// مسارات الفواتير
Route::resource('invoices', InvoiceController::class);

//
Route::get('/fixed_fees/{id}', [FixedFeeController::class, 'getFeeDetails']);

require __DIR__ . '/auth.php';
