<?php

use App\Domain\Channels\Controllers\ChannelCustomerController;
use App\Domain\Channels\Controllers\ChannelCustomerManagementController;
use App\Domain\Channels\Controllers\ChannelOrderController;
use App\Domain\Channels\Controllers\ChannelPaymentIntentController;
use App\Domain\Channels\Controllers\ChannelSsoController;
use App\Domain\Channels\Controllers\ChannelWebhookController;
use App\Domain\Woo\Controllers\WooCustomerController;
use App\Domain\Woo\Controllers\WooOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'api.rate', 'channel.auth'])->prefix('api/v1/channels')->group(function () {
    Route::post('init-dd', [ChannelOrderController::class, 'initDd']);
    Route::post('payment-intents', [ChannelPaymentIntentController::class, 'store']);
    Route::post('customers/update-credit', [ChannelCustomerController::class, 'updateCredit']);
    Route::post('customers', [ChannelCustomerManagementController::class, 'upsert']);
    Route::post('webhooks/test', [ChannelWebhookController::class, 'test']);
    Route::post('sso', [ChannelSsoController::class, 'create']);
});

Route::middleware(['api', 'api.rate', 'woo.auth'])->prefix('api/v1/woo')->group(function () {
    Route::post('orders/init-dd', [WooOrderController::class, 'initDd']);
    Route::post('customers/update-credit', [WooCustomerController::class, 'updateCredit']);
});
