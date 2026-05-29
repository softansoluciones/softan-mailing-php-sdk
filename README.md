# Softan Mailing SDK (PHP)

[![Latest Stable Version](https://img.shields.io/packagist/v/softan/mailing-php-sdk.svg)](https://packagist.org/packages/softan/mailing-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/softan/mailing-php-sdk.svg)](https://packagist.org/packages/softan/mailing-php-sdk)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)

## Descripción

SDK oficial para integrar aplicaciones con Softan Mailing en PHP. Expone un método estático de alto nivel para enviar correos electrónicos mediante plantillas existentes.

Las credenciales de acceso a la API están embebidas en el SDK (`sdk_meta.json`). No se requiere ninguna configuración manual de API keys.

> **Nota:** La gestión de templates, cuentas SMTP e instancias se realiza a través del módulo Softan Mailing Module v2 o directamente desde el board de administración. Este SDK está diseñado exclusivamente para el envío de correos desde aplicaciones cliente.

## Requisitos

- PHP 8.1 o superior
- Composer 2
- Extensiones: `ext-curl`, `ext-json`

## Instalación

```bash
composer require softan/mailing-php-sdk:^0.2.0
```

Listo. No se necesita ningún paso adicional — las credenciales están embebidas.

### Alternativa: instalación desde GitHub (VCS)

```bash
composer config repositories.softan-mailing vcs https://github.com/softansoluciones/softan-mailing-php-sdk
composer require softan/mailing-php-sdk:dev-main
```

## Quickstart

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use SoftanMailing\Services;

// Enviar un correo usando una plantilla existente
$result = Services::sendByTemplate([
    'template_key'    => 'tpl_1234567890_ab12',
    'recipient_email' => 'usuario@example.com',
    'recipient_name'  => 'Juan Pérez',
    'email_params'    => [
        'name' => 'Juan',
        'code' => '998877',
    ],
]);

var_dump($result);
```

## Uso en código

### Enviar correo por plantilla

```php
use SoftanMailing\Services;

$result = Services::sendByTemplate([
    'template_key'    => 'tpl_1234567890_ab12',  // Clave del template (tpl_...)
    'recipient_email' => 'usuario@example.com',
    'recipient_name'  => 'Juan Pérez',
    'email_params'    => [                         // Mapa clave-valor con los parámetros del template
        'name' => 'Juan',
        'code' => '998877',
    ],
]);
```

**Campos requeridos:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `template_key` | string | Clave del template (`tpl_...`) |
| `recipient_email` | string | Correo del destinatario |
| `recipient_name` | string | Nombre del destinatario |
| `email_params` | array | Parámetros del template (mapa clave-valor) |

**Respuesta exitosa:**

```json
{
  "success": true,
  "code": "common.ok",
  "message": "Email sent successfully.",
  "data": {}
}
```

## Entornos

El SDK incluye credenciales para `stg` y `prod`. El entorno por defecto es `stg`.

En `stg`, todos los correos son redirigidos al destinatario sandbox configurado en el servidor (SMTP sandbox). En `prod`, los correos se envían al destinatario real.

### Cambiar el entorno activo

El SDK usa inicialización lazy: si el proyecto pre-configura `SDK::$META` y `SDK::$CONFIG` antes de la primera llamada a un servicio, esos valores se usan durante todo el ciclo de vida de la request.

```php
use SoftanMailing\SDK;
use SoftanMailing\Services;

// Forzar entorno prod (llamar antes del primer Services::*)
SDK::$META   = SDK::loadJson(SDK::META_PATH);
SDK::$CONFIG = ['active_environment' => 'prod'];

// Todas las llamadas siguientes usarán prod
$result = Services::sendByTemplate([...]);
```

Si no se realiza ninguna inicialización previa, el SDK usa `stg` como entorno por defecto (definido en `sdk_meta.json`).

## Configuración

Las credenciales de API están embebidas en `sdk_meta.json` (XOR+base64). No es necesario ni recomendable crear un `sdk_config.json` con credenciales.

El único uso válido de `sdk_config.json` es sobrescribir el entorno activo cuando se prefiere configuración en archivo en lugar de código:

```json
{
  "active_environment": "prod"
}
```

`sdk_config.json` debe ir en `.gitignore` si se crea. No está incluido en el repositorio del SDK.

## TLS

La verificación TLS está habilitada por defecto. Para desarrollo puedes desactivarla por llamada:

```php
Services::sendByTemplate($payload, null, false);  // tercer parámetro: $verifyTLS
```

## Compatibilidad

- PHP: 8.1+
- Sistemas: Windows, Linux, macOS

## Desarrollo

```bash
composer install
composer test
```

CI: el workflow en `.github/workflows/ci.yml` valida Composer e integra PHPUnit en PHP 8.1/8.2/8.3.

## Licencia

MIT (ver `composer.json`).
