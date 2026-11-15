<?php

use App\Http\Controllers\Api\MillController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/catalog', [MillController::class, 'catalog']);
    Route::get('/catalog/{fabricProduct}', [MillController::class, 'catalogItem']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [MillController::class, 'dashboard']);
        Route::get('/trace/{code}', [MillController::class, 'trace']);
        Route::post('/dye-batches/{dyeBatch}/analyze', [MillController::class, 'analyze']);
        Route::post('/documents', [MillController::class, 'document']);
        Route::get('/reports/{report}', [MillController::class, 'report']);
        Route::post('/inventory/issue', [MillController::class, 'issue']);
        Route::post('/orders/{order}/work-orders', [MillController::class, 'workOrders']);
        Route::get('/alerts', [MillController::class, 'alerts']);
        Route::post('/attendance/sync', [MillController::class, 'syncAttendance']);
    });
});
