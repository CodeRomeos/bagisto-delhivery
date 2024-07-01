<?php

use Illuminate\Support\Facades\Route;
use CodeRomeos\BagistoDelhivery\Http\Controllers\Admin\BagistoDelhiveryController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/bagistodelhivery'], function () {
    Route::controller(BagistoDelhiveryController::class)->group(function () {
        Route::get('', 'index')->name('admin.bagistodelhivery.index');
    });
});