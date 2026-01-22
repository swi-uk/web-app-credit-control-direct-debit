<?php

namespace App\Domain\Bureau\Services;

use App\Domain\Bureau\Connectors\AbsoluteApiConnector;
use App\Domain\Bureau\Connectors\AbsoluteSftpConnector;
use App\Domain\Bureau\Connectors\NullConnector;
use App\Domain\Bureau\Contracts\BureauConnectorInterface;
use App\Domain\Merchants\Models\MerchantSite;
use App\Domain\Security\Models\MerchantSiteSecret;

class BureauService
{
    public function connectorFor(MerchantSite $site): BureauConnectorInterface
    {
        $settings = $site->settings_json ?? [];
        $mode = $settings['bureau_mode'] ?? null;
        $sftpKey = $settings['sftp']['private_key_secret_id'] ?? null;
        $apiTokenKey = $settings['api']['token_secret_id'] ?? null;

        return match ($mode) {
            'sftp' => new AbsoluteSftpConnector([
                'host' => $settings['sftp']['host'] ?? null,
                'port' => $settings['sftp']['port'] ?? 22,
                'username' => $settings['sftp']['username'] ?? null,
                'private_key' => $sftpKey ? MerchantSiteSecret::valueFor($site->id, $sftpKey) : null,
                'password' => $settings['sftp']['password'] ?? null,
                'inbound_dir' => $settings['sftp']['inbound_dir'] ?? null,
                'outbound_dir' => $settings['sftp']['outbound_dir'] ?? null,
                'archive_dir' => $settings['sftp']['archive_dir'] ?? null,
            ]),
            'api' => new AbsoluteApiConnector([
                'base_url' => $settings['api']['base_url'] ?? null,
                'token' => $apiTokenKey ? MerchantSiteSecret::valueFor($site->id, $apiTokenKey) : null,
            ]),
            default => new NullConnector(),
        };
    }
}
