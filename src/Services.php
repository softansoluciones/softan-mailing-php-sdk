<?php
namespace SoftanMailing;

final class Services
{
    /**
     * POST /manager/send-by-template — Enviar un correo usando una plantilla existente.
     *
     * Campos requeridos:
     *   - template_key    (string)  clave tpl_... del template
     *   - recipient_email (string)
     *   - recipient_name  (string)
     *   - email_params    (array)   mapa clave-valor con los parámetros del template
     *
     * @param array       $payload    Datos del envío
     * @param array|null  $headers    Headers personalizados (opcional)
     * @param bool        $verifyTLS  Verificación TLS (desactivar solo en desarrollo)
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
}
