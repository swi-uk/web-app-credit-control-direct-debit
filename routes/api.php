<?php

use App\Domain\Woo\Controllers\WooCustomerController;
use App\Domain\Woo\Controllers\WooOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('woo.auth')->prefix('api/v1/woo')->group(function () {
    Route::post('orders/init-dd', [WooOrderController::class, 'initDd']);
    Route::post('customers/update-credit', [WooCustomerController::class, 'updateCredit']);
});
