<?php
namespace SoftanMailing;

final class Headers
{
    /**
     * Build the runtime headers for Softan Mailing API requests.
     * Only requires X-API-KEY — no HMAC signing needed.
     */
    public static function buildRuntimeHeaders(?string $env = null): array
    {
        $apiKey = Config::getApiKey($env);

        if ($apiKey === '') {
            throw new \RuntimeException(
                'SoftanMailing: api_key is not configured. Run bin/install.php or set up sdk_config.json.'
            );
        }

        return [
            'Content-Type' => 'application/json',
            'X-API-KEY'    => $apiKey,
        ];
    }
}
