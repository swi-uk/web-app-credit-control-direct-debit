<?php

namespace App\Console\Commands;

use App\Domain\Billing\Models\MerchantSubscription;
use App\Domain\Billing\Services\BillingService;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'ccdd:generate-invoices';
    protected $description = 'Generate invoices for merchants';

    public function __construct(private readonly BillingService $billingService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $subscriptions = MerchantSubscription::with('merchant', 'plan')
            ->whereIn('status', ['active', 'trial'])
            ->where('current_period_end', '<=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $this->billingService->generateInvoice($subscription);

            $subscription->current_period_start = $subscription->current_period_end;
            $subscription->current_period_end = $subscription->current_period_end->copy()->addMonth();
            $subscription->save();
        }

        $this->info('Invoices generated: ' . $subscriptions->count());

        return self::SUCCESS;
    }
}
