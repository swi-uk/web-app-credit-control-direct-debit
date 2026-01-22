<?php

namespace App\Domain\Bureau\Services;

use App\Domain\Bureau\Connectors\AbsoluteApiConnector;
use App\Domain\Bureau\Connectors\AbsoluteSftpConnector;
use App\Domain\Bureau\Connectors\NullConnector;
use App\Domain\Bureau\Contracts\BureauConnectorInterface;
use App\Domain\Merchants\Models\MerchantSite;

class BureauService
{
    public function connectorFor(MerchantSite $site): BureauConnectorInterface
    {
        $settings = $site->settings_json ?? [];
        $mode = $settings['bureau_mode'] ?? null;
        $secret = $site->secrets()->first();

        return match ($mode) {
            'sftp' => new AbsoluteSftpConnector([
                'host' => $settings['sftp']['host'] ?? null,
                'port' => $settings['sftp']['port'] ?? 22,
                'username' => $settings['sftp']['username'] ?? null,
                'private_key' => $secret?->sftp_private_key,
                'password' => $secret?->sftp_password,
                'inbound_dir' => $settings['sftp']['inbound_dir'] ?? null,
                'outbound_dir' => $settings['sftp']['outbound_dir'] ?? null,
            ]),
            'api' => new AbsoluteApiConnector([
                'base_url' => $settings['api']['base_url'] ?? null,
                'token' => $secret?->api_token ?? ($settings['api']['token'] ?? null),
            ]),
            default => new NullConnector(),
        };
    }
}
