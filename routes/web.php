<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\BacsReportController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CreditTierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\Ops\MonitoringController;
use App\Http\Controllers\Admin\Portfolio\DashboardController as PortfolioDashboardController;
use App\Http\Controllers\Connectors\ShopifyController;
use App\Http\Controllers\Onboarding\OnboardingController;
use App\Http\Controllers\DdiController;
use App\Http\Controllers\MandateUpdateController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\DocumentController as PortalDocumentController;
use App\Http\Controllers\Portal\MandateController as PortalMandateController;
use App\Http\Controllers\Portal\RefundController as PortalRefundController;
use Illuminate\Support\Facades\Route;

Route::get('/ddi/{token}', [DdiController::class, 'show']);
Route::post('/ddi/{token}', [DdiController::class, 'submit']);
Route::get('/mandate/update/{token}', [MandateUpdateController::class, 'show'])->name('mandate.update.show');
Route::post('/mandate/update/{token}', [MandateUpdateController::class, 'submit'])->name('mandate.update.submit');

Route::prefix('admin')->group(function () {
    Route::get('sites', [SiteController::class, 'index'])->name('admin.sites.index');
    Route::get('sites/create', [SiteController::class, 'create'])->name('admin.sites.create');
    Route::post('sites', [SiteController::class, 'store'])->name('admin.sites.store');

    Route::get('customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::post('customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');

    Route::get('payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('payments/{payment}/mark-collected', [PaymentController::class, 'markCollected'])
        ->name('admin.payments.markCollected');

    Route::get('bacs-reports/upload', [BacsReportController::class, 'create'])->name('admin.bacs.upload');
    Route::post('bacs-reports/upload', [BacsReportController::class, 'store']);

    Route::get('credit-tiers', [CreditTierController::class, 'index'])->name('admin.credit_tiers.index');
    Route::get('credit-tiers/create', [CreditTierController::class, 'create'])->name('admin.credit_tiers.create');
    Route::post('credit-tiers', [CreditTierController::class, 'store'])->name('admin.credit_tiers.store');
    Route::get('credit-tiers/{creditTier}/edit', [CreditTierController::class, 'edit'])->name('admin.credit_tiers.edit');
    Route::post('credit-tiers/{creditTier}', [CreditTierController::class, 'update'])->name('admin.credit_tiers.update');

    Route::get('refunds', [RefundController::class, 'index'])->name('admin.refunds.index');
    Route::post('refunds/{refundRequest}/approve', [RefundController::class, 'approve'])->name('admin.refunds.approve');
    Route::post('refunds/{refundRequest}/deny', [RefundController::class, 'deny'])->name('admin.refunds.deny');
    Route::post('refunds/{refundRequest}/processed', [RefundController::class, 'markProcessed'])->name('admin.refunds.processed');

    Route::get('export/payments.csv', [ExportController::class, 'payments'])->name('admin.export.payments');
    Route::get('export/customers.csv', [ExportController::class, 'customers'])->name('admin.export.customers');

    Route::get('sites/{merchantSite}/api-keys', [ApiKeyController::class, 'index'])->name('admin.api_keys.index');
    Route::get('sites/{merchantSite}/api-keys/create', [ApiKeyController::class, 'create'])->name('admin.api_keys.create');
    Route::post('sites/{merchantSite}/api-keys/{merchantSiteApiKey}/revoke', [ApiKeyController::class, 'revoke'])
        ->name('admin.api_keys.revoke');

    Route::get('ops', [MonitoringController::class, 'index'])->name('admin.ops.index');
    Route::get('billing', [BillingController::class, 'index'])->name('admin.billing.index');
    Route::get('portfolio', [PortfolioDashboardController::class, 'index'])->name('admin.portfolio.index');
});

Route::get('/portal/login', [PortalAuthController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [PortalAuthController::class, 'sendLink'])->name('portal.login.send');
Route::get('/portal/auth/{token}', [PortalAuthController::class, 'consumeToken'])->name('portal.auth');
Route::get('/portal/sso/{token}', [PortalAuthController::class, 'consumeSso'])->name('portal.sso');
Route::post('/portal/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');

Route::get('/portal', [PortalDashboardController::class, 'index'])->name('portal.dashboard');
Route::get('/portal/payments', [PortalDashboardController::class, 'payments'])->name('portal.payments');
Route::get('/portal/mandates', [PortalDashboardController::class, 'mandates'])->name('portal.mandates');
Route::post('/portal/mandate-update', [PortalMandateController::class, 'createUpdateLink'])->name('portal.mandate.update');

Route::get('/portal/refunds/create', [PortalRefundController::class, 'create'])->name('portal.refunds.create');
Route::post('/portal/refunds', [PortalRefundController::class, 'store'])->name('portal.refunds.store');

Route::get('/portal/documents/{document}', [PortalDocumentController::class, 'download'])->name('portal.documents.download');
Route::get('/portal/documents/mandate/{mandate}', [PortalDocumentController::class, 'mandateReceipt'])->name('portal.documents.mandate');
Route::get('/portal/documents/advance/{payment}', [PortalDocumentController::class, 'advanceNotice'])->name('portal.documents.advance');
Route::get('/portal/documents/unpaid/{payment}', [PortalDocumentController::class, 'unpaidNotice'])->name('portal.documents.unpaid');
Route::get('/portal/documents/refund/{refundRequest}', [PortalDocumentController::class, 'refundNotice'])->name('portal.documents.refund');

Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
Route::post('/onboarding/steps/{step}', [OnboardingController::class, 'update'])->name('onboarding.update');

Route::get('/connectors/shopify/install', [ShopifyController::class, 'install'])->name('connectors.shopify.install');
Route::get('/connectors/shopify/callback', [ShopifyController::class, 'callback'])->name('connectors.shopify.callback');
Route::post('/connectors/shopify/webhook', [ShopifyController::class, 'webhook'])->name('connectors.shopify.webhook');
