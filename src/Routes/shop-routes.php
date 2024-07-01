<?php

use Illuminate\Support\Facades\Route;
use CodeRomeos\BagistoDelhivery\Http\Controllers\Shop\BagistoDelhiveryController;

// Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'bagistodelhivery'], function () {
//     Route::get('', [BagistoDelhiveryController::class, 'index'])->name('shop.bagistodelhivery.index');
// });

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'delhivery'], function () {
    Route::get('tracking', [BagistoDelhiveryController::class, 'tracking'])->name('shop.bagistodelhivery.tracking');
    Route::get('estimated-delivery', [BagistoDelhiveryController::class, 'getEstimatedDelivery'])->name('shop.bagistodelhivery.estimateddelivery');
    Route::get('pincode-availability', [BagistoDelhiveryController::class, 'checkPincodeAvailability'])->name('shop.bagistodelhivery.pincode_availability');
});
