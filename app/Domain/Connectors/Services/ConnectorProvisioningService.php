<?php

namespace App\Domain\Connectors\Services;

use App\Domain\Connectors\Models\ConnectorInstall;
use App\Domain\Merchants\Models\MerchantSite;

class ConnectorProvisioningService
{
    public function markInstalled(MerchantSite $site, string $connector, ?string $accessToken = null, ?string $refreshToken = null): ConnectorInstall
    {
        return ConnectorInstall::create([
            'merchant_site_id' => $site->id,
            'connector' => $connector,
            'status' => 'installed',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'installed_at' => now(),
        ]);
    }
}
