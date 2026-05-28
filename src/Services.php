<?php
namespace SoftanMailing;

final class Services
{
    // ----------------------------------------------------------------
    // Templates
    // ----------------------------------------------------------------

    /**
     * GET /templates — List all templates.
     */
    public static function listTemplates(?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('templates', 'base');
        return Client::request($endpoint, 'GET', $headers, null, null, $verifyTLS);
    }

    /**
     * GET /templates/show/{id} — Show a single template.
     */
    public static function showTemplate(int $id, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('templates', 'show') . '/' . $id;
        return Client::request($endpoint, 'GET', $headers, null, null, $verifyTLS);
    }

    /**
     * POST /templates — Create a new template.
     *
     * Required payload fields:
     *   - template_html        (string)
     *   - template_name        (string)
     *   - template_description (string)
     *   - template_subject     (string)
     *   - template_params      (string)  comma-separated param names, e.g. "name,code"
     *   - app_id               (string)
     *   - account_identifier   (string)
     */
    public static function createTemplate(array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requireFields($payload, [
            'template_html', 'template_name', 'template_description',
            'template_subject', 'template_params', 'app_id', 'account_identifier',
        ]);
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('templates', 'base');
        return Client::request($endpoint, 'POST', $headers, $payload, null, $verifyTLS);
    }

    /**
     * PUT /templates/{id} — Update an existing template.
     *
     * Same required fields as createTemplate().
     */
    public static function updateTemplate(int $id, array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        self::requireFields($payload, [
            'template_html', 'template_name', 'template_description',
            'template_subject', 'template_params', 'app_id', 'account_identifier',
        ]);
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('templates', 'base') . '/' . $id;
        return Client::request($endpoint, 'PUT', $headers, $payload, null, $verifyTLS);
    }

    /**
     * DELETE /templates/{id} — Delete a template.
     */
    public static function deleteTemplate(int $id, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('templates', 'base') . '/' . $id;
        return Client::request($endpoint, 'DELETE', $headers, null, null, $verifyTLS);
    }

    // ----------------------------------------------------------------
    // Accounts
    // ----------------------------------------------------------------

    /**
     * GET /accounts — List all SMTP accounts.
     */
    public static function listAccounts(?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('accounts', 'base');
        return Client::request($endpoint, 'GET', $headers, null, null, $verifyTLS);
    }

    /**
     * GET /accounts/show/{id} — Show a single SMTP account.
     */
    public static function showAccount(int $id, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('accounts', 'show') . '/' . $id;
        return Client::request($endpoint, 'GET', $headers, null, null, $verifyTLS);
    }

    /**
     * POST /accounts — Create a new SMTP account.
     *
     * Required payload fields:
     *   - account_email    (string)
     *   - account_name     (string)
     *   - account_host     (string)
     *   - account_port     (int)
     *   - account_password (string)
     *   - user_id          (int)
     */
    public static function createAccount(array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requireFields($payload, [
            'account_email', 'account_name', 'account_host',
            'account_port', 'account_password', 'user_id',
        ]);
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('accounts', 'base');
        return Client::request($endpoint, 'POST', $headers, $payload, null, $verifyTLS);
    }

    /**
     * PUT /accounts/{id} — Update an existing SMTP account.
     *
     * Same required fields as createAccount().
     */
    public static function updateAccount(int $id, array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        self::requireFields($payload, [
            'account_email', 'account_name', 'account_host',
            'account_port', 'account_password', 'user_id',
        ]);
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('accounts', 'base') . '/' . $id;
        return Client::request($endpoint, 'PUT', $headers, $payload, null, $verifyTLS);
    }

    /**
     * DELETE /accounts/{id} — Delete an SMTP account.
     */
    public static function deleteAccount(int $id, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requirePositive($id, 'id');
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('accounts', 'base') . '/' . $id;
        return Client::request($endpoint, 'DELETE', $headers, null, null, $verifyTLS);
    }

    // ----------------------------------------------------------------
    // Manager
    // ----------------------------------------------------------------

    /**
     * POST /manager/send-by-template — Send an email using an existing template.
     *
     * Required payload fields:
     *   - template_key    (string)  the tpl_... key of the template
     *   - recipient_email (string)
     *   - recipient_name  (string)
     *   - email_params    (array)   key-value map matching the template's param names
     */
    public static function sendByTemplate(array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requireFields($payload, ['template_key', 'recipient_email', 'recipient_name', 'email_params']);
        if (!is_array($payload['email_params'])) {
            throw new \InvalidArgumentException("Field 'email_params' must be an array.");
        }
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('manager', 'send_by_template');
        return Client::request($endpoint, 'POST', $headers, $payload, null, $verifyTLS);
    }

    /**
     * POST /manager/create-instance — Create SMTP account + template in one call.
     *
     * Required payload fields:
     *   - account_email        (string)
     *   - account_name         (string)
     *   - account_host         (string)
     *   - account_port         (int)
     *   - account_password     (string)
     *   - template_html        (string)
     *   - template_name        (string)
     *   - template_description (string)
     *   - template_subject     (string)
     *   - template_params      (string)
     *   - app_id               (string)
     *   - user_id              (int)
     */
    public static function createInstance(array $payload, ?array $headers = null, bool $verifyTLS = true): array
    {
        SDK::init();
        self::requireFields($payload, [
            'account_email', 'account_name', 'account_host', 'account_port', 'account_password',
            'template_html', 'template_name', 'template_description',
            'template_subject', 'template_params', 'app_id', 'user_id',
        ]);
        $headers  = $headers ?: Headers::buildRuntimeHeaders();
        $endpoint = Config::resolveEndpoint('manager', 'create_instance');
        return Client::request($endpoint, 'POST', $headers, $payload, null, $verifyTLS);
    }

    // ----------------------------------------------------------------
    // Internal helpers
    // ----------------------------------------------------------------

    private static function requireFields(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                throw new \InvalidArgumentException("Missing required field: '{$field}'.");
            }
        }
    }

    private static function requirePositive(int $value, string $name): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException("'{$name}' must be a positive integer.");
        }
    }
}
