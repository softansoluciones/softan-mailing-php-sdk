<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SoftanMailing\SDK;
use SoftanMailing\Services;

/**
 * Tests that Services::sendByTemplate() throws InvalidArgumentException for
 * invalid input BEFORE any HTTP call is attempted.
 *
 * SDK::init() loads the local sdk_meta.json (present in repo). All assertions
 * happen during the validation phase; no network access occurs.
 */
class ServicesValidationTest extends TestCase
{
    protected function setUp(): void
    {
        SDK::$META   = SDK::loadJson(SDK::META_PATH);
        SDK::$CONFIG = [];
    }

    public function test_send_by_template_rejects_empty_payload(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([]);
    }

    public function test_send_by_template_rejects_missing_template_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            // template_key missing
            'recipient_email' => 'user@example.com',
            'recipient_name'  => 'John Doe',
            'email_params'    => ['name' => 'John'],
        ]);
    }

    public function test_send_by_template_rejects_missing_recipient_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            'template_key'   => 'tpl_abc123',
            // recipient_email missing
            'recipient_name' => 'John Doe',
            'email_params'   => ['name' => 'John'],
        ]);
    }

    public function test_send_by_template_rejects_missing_recipient_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            'template_key'    => 'tpl_abc123',
            'recipient_email' => 'user@example.com',
            // recipient_name missing
            'email_params'    => ['name' => 'John'],
        ]);
    }

    public function test_send_by_template_rejects_missing_email_params(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            'template_key'    => 'tpl_abc123',
            'recipient_email' => 'user@example.com',
            'recipient_name'  => 'John Doe',
            // email_params missing
        ]);
    }

    public function test_send_by_template_rejects_email_params_as_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            'template_key'    => 'tpl_abc123',
            'recipient_email' => 'user@example.com',
            'recipient_name'  => 'John Doe',
            'email_params'    => 'not-an-array',
        ]);
    }

    public function test_send_by_template_rejects_empty_string_for_template_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Services::sendByTemplate([
            'template_key'    => '',  // empty string treated as missing
            'recipient_email' => 'user@example.com',
            'recipient_name'  => 'John Doe',
            'email_params'    => ['name' => 'John'],
        ]);
    }
}
