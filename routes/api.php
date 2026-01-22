<?php

use App\Domain\Channels\Controllers\ChannelCustomerController;
use App\Domain\Channels\Controllers\ChannelOrderController;
use App\Domain\Woo\Controllers\WooCustomerController;
use App\Domain\Woo\Controllers\WooOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('channel.auth')->prefix('api/v1/channels')->group(function () {
    Route::post('init-dd', [ChannelOrderController::class, 'initDd']);
    Route::post('customers/update-credit', [ChannelCustomerController::class, 'updateCredit']);
});

Route::middleware('woo.auth')->prefix('api/v1/woo')->group(function () {
    Route::post('orders/init-dd', [WooOrderController::class, 'initDd']);
    Route::post('customers/update-credit', [WooCustomerController::class, 'updateCredit']);
});
