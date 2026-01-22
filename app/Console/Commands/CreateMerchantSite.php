<?php

namespace App\Console\Commands;

use App\Domain\Merchants\Models\Merchant;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Security\Models\MerchantSiteApiKey;
use App\Support\Tokens\TokenService;
use Illuminate\Console\Command;

class CreateMerchantSite extends Command
{
    protected $signature = 'ccdd:create-merchant-site {merchant_name} {site_id} {base_url}';
    protected $description = 'Create a merchant site with API keys';

    public function __construct(private readonly TokenService $tokenService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $merchantName = $this->argument('merchant_name');
        $siteId = $this->argument('site_id');
        $baseUrl = rtrim($this->argument('base_url'), '/');

        $merchant = Merchant::firstOrCreate(
            ['name' => $merchantName],
            ['plan' => 'starter', 'status' => 'active']
        );

        $apiKey = $this->tokenService->generate();
        $webhookSecret = $this->tokenService->generate();

        $site = MerchantSite::create([
            'merchant_id' => $merchant->id,
            'site_id' => $siteId,
            'base_url' => $baseUrl,
            'api_key_hash' => $this->tokenService->hash($apiKey),
            'webhook_secret' => $webhookSecret,
        ]);

        MerchantSiteApiKey::create([
            'merchant_site_id' => $site->id,
            'key_hash' => $this->tokenService->hash($apiKey),
            'name' => 'Initial key',
            'status' => 'active',
        ]);

        $this->info('Merchant site created: ' . $site->id);
        $this->line('API Key: ' . $apiKey);
        $this->line('Webhook Secret: ' . $webhookSecret);

        return self::SUCCESS;
    }
}
