<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\DdiController;
use Illuminate\Support\Facades\Route;

Route::get('/ddi/{token}', [DdiController::class, 'show']);
Route::post('/ddi/{token}', [DdiController::class, 'submit']);

Route::prefix('admin')->group(function () {
    Route::get('sites', [SiteController::class, 'index'])->name('admin.sites.index');
    Route::get('sites/create', [SiteController::class, 'create'])->name('admin.sites.create');
    Route::post('sites', [SiteController::class, 'store'])->name('admin.sites.store');

    Route::get('customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::post('customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
});
