<?php

use App\Http\Controllers\PortalController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Front-Facing Department Requisition Portal (RMPH Clinical Wards)
Route::get('/portal', [PortalController::class, 'index'])->name('portal');
Route::post('/portal/submit', [PortalController::class, 'submit'])->name('portal.submit');
Route::post('/submit-request', [PortalController::class, 'submit']); // Compatibility alias

// Printable Requisition Slip (RIS) - Accessible for ward printing
Route::get('/requisition/{stockRequest}', [PrintController::class, 'requisition'])->name('requisition.print');

// Authenticated Consignment Oversight & Audit Reports
Route::middleware(['auth'])->group(function () {
    Route::get('/stockcard/{item}', [PrintController::class, 'stockcard'])->name('stockcard.print');
    Route::get('/bincard/{item}', [PrintController::class, 'bincard'])->name('bincard.print');
    Route::get('/rapc/{item?}', [PrintController::class, 'rapc'])->name('rapc.print');
    Route::get('/purchase-request/critical', [PrintController::class, 'purchaseRequest'])->name('purchase-request.critical');
    Route::get('/purchase-request/{item}', [PrintController::class, 'purchaseRequest'])->name('purchase-request.item');
    Route::get('/item-usage/{item}', [PrintController::class, 'itemUsage'])->name('item-usage.print');
    Route::get('/inventory-reports/print', [PrintController::class, 'inventoryReport'])->name('inventory-reports.print');
    Route::get('/stcg/{supplier}', [PrintController::class, 'stcg'])->name('stcg.print');
});
