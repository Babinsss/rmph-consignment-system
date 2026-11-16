<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('requesting_unit') && ! $user->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer'])) {
            return redirect('/portal');
        }

        return redirect('/admin');
    }

    return redirect('/admin/login');
});

Route::middleware(['auth'])->prefix('reports')->group(function () {
    Route::get('/stock-on-hand/pdf', [ReportController::class, 'stockOnHandPdf'])->name('reports.stock-on-hand.pdf');
    Route::get('/stock-on-hand/csv', [ReportController::class, 'stockOnHandCsv'])->name('reports.stock-on-hand.csv');

    Route::get('/near-expiry/pdf', [ReportController::class, 'nearExpiryPdf'])->name('reports.near-expiry.pdf');
    Route::get('/near-expiry/csv', [ReportController::class, 'nearExpiryCsv'])->name('reports.near-expiry.csv');

    Route::get('/deliveries/pdf', [ReportController::class, 'deliveriesPdf'])->name('reports.deliveries.pdf');
    Route::get('/deliveries/csv', [ReportController::class, 'deliveriesCsv'])->name('reports.deliveries.csv');

    Route::get('/unit-requests/pdf', [ReportController::class, 'unitRequestsPdf'])->name('reports.unit-requests.pdf');
    Route::get('/unit-requests/csv', [ReportController::class, 'unitRequestsCsv'])->name('reports.unit-requests.csv');

    Route::get('/stock-movements/pdf', [ReportController::class, 'stockMovementsPdf'])->name('reports.stock-movements.pdf');
    Route::get('/stock-movements/csv', [ReportController::class, 'stockMovementsCsv'])->name('reports.stock-movements.csv');

    Route::get('/requests/{requisition}/issue-slip', [ReportController::class, 'requestIssueSlipPdf'])->name('reports.requests.issue-slip');
});
