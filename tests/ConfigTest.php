<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SoftanMailing\SDK;
use SoftanMailing\Config;

/**
 * Tests Config static methods using the real sdk_meta.json.
 * No HTTP calls are made — SDK state is loaded from file.
 */
class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        // Load real sdk_meta.json; clear sdk_config.json (not present in CI)
        SDK::$META   = SDK::loadJson(SDK::META_PATH);
        SDK::$CONFIG = [];
    }

    public function test_get_app_id_returns_correct_value(): void
    {
        $this->assertSame('SOM-096', Config::getAppId());
    }

    public function test_get_active_environment_defaults_to_stg_when_no_config(): void
    {
        // SDK::$CONFIG is empty → falls back to sdk_meta default_environment
        $this->assertSame('stg', Config::getActiveEnvironment());
    }

    public function test_get_active_environment_uses_config_when_present(): void
    {
        SDK::$CONFIG = ['active_environment' => 'prod'];
        $this->assertSame('prod', Config::getActiveEnvironment());
    }

    public function test_get_base_url_stg_is_nonempty_and_ends_with_slash(): void
    {
        $url = Config::getBaseUrl('stg');
        $this->assertNotEmpty($url);
        $this->assertStringEndsWith('/', $url);
    }

    public function test_get_base_url_prod_is_nonempty_and_ends_with_slash(): void
    {
        $url = Config::getBaseUrl('prod');
        $this->assertNotEmpty($url);
        $this->assertStringEndsWith('/', $url);
    }

    public function test_get_base_url_unknown_env_returns_empty(): void
    {
        $this->assertSame('', Config::getBaseUrl('nonexistent'));
    }

    public function test_get_api_key_stg_decodes_to_nonempty_string(): void
    {
        $key = Config::getApiKey('stg');
        $this->assertNotEmpty($key, 'Decoded stg API key must not be empty.');
    }

    public function test_get_api_key_prod_decodes_to_nonempty_string(): void
    {
        $key = Config::getApiKey('prod');
        $this->assertNotEmpty($key, 'Decoded prod API key must not be empty.');
    }

    public function test_get_api_key_stg_and_prod_are_different(): void
    {
        $this->assertNotSame(Config::getApiKey('stg'), Config::getApiKey('prod'));
    }

    public function test_get_api_key_unknown_env_returns_empty(): void
    {
        $this->assertSame('', Config::getApiKey('nonexistent'));
    }

    public function test_resolve_endpoint_send_by_template(): void
    {
        $endpoint = Config::resolveEndpoint('manager', 'send_by_template');
        $this->assertStringContainsString('send', $endpoint);
        $this->assertStringContainsString('template', $endpoint);
    }

    public function test_resolve_endpoint_invalid_key_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Config::resolveEndpoint('nonexistent_resource', 'action');
    }

    public function test_resolve_endpoint_invalid_leaf_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Config::resolveEndpoint('manager', 'nonexistent_action');
    }

    public function test_get_meta_returns_default_for_missing_key(): void
    {
        $this->assertNull(Config::getMeta('key_that_does_not_exist'));
        $this->assertSame('fallback', Config::getMeta('key_that_does_not_exist', 'fallback'));
    }
}
