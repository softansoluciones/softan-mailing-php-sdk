# Softan Mailing SDK (PHP)

[![Latest Stable Version](https://img.shields.io/packagist/v/softan/mailing-php-sdk.svg)](https://packagist.org/packages/softan/mailing-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/softan/mailing-php-sdk.svg)](https://packagist.org/packages/softan/mailing-php-sdk)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)

## Descripción

SDK oficial para integrar aplicaciones con Softan Mailing en PHP. Expone métodos estáticos de alto nivel para gestionar templates, cuentas SMTP y envío de correos mediante plantillas.

## Requisitos

- PHP 8.1 o superior
- Composer 2
- Extensiones: `ext-curl`, `ext-json`

## Instalación

```bash
composer require softan/mailing-php-sdk:^0.1.0
```

Luego inicializa la configuración con tu API key:

```bash
php vendor/bin/install.php
```

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

### Templates

```php
use SoftanMailing\Services;

// Listar todos los templates
$list = Services::listTemplates();

// Ver un template por ID
$template = Services::showTemplate(42);

// Crear un template
$created = Services::createTemplate([
    'template_html'        => '<h1>Hola {{name}}</h1>',
    'template_name'        => 'Bienvenida',
    'template_description' => 'Correo de bienvenida',
    'template_subject'     => 'Bienvenido a nuestra plataforma',
    'template_params'      => 'name',
    'app_id'               => 'SOM-XXXX',
    'account_identifier'   => 'acc_xxxxxxxxxxxx',
]);

// Actualizar un template
$updated = Services::updateTemplate(42, [
    'template_html'        => '<h1>Hola {{name}}, código: {{code}}</h1>',
    'template_name'        => 'Bienvenida v2',
    'template_description' => 'Correo de bienvenida actualizado',
    'template_subject'     => 'Bienvenido',
    'template_params'      => 'name,code',
    'app_id'               => 'SOM-XXXX',
    'account_identifier'   => 'acc_xxxxxxxxxxxx',
]);

// Eliminar un template
$deleted = Services::deleteTemplate(42);
```

### Accounts (cuentas SMTP)

```php
// Listar cuentas
$accounts = Services::listAccounts();

// Ver una cuenta
$account = Services::showAccount(5);

// Crear cuenta SMTP
$created = Services::createAccount([
    'account_email'    => 'noreply@miempresa.com',
    'account_name'     => 'Mi Empresa',
    'account_host'     => 'smtp.miempresa.com',
    'account_port'     => 587,
    'account_password' => 'password123',
    'user_id'          => 1,
]);

// Actualizar cuenta
$updated = Services::updateAccount(5, [
    'account_email'    => 'noreply@miempresa.com',
    'account_name'     => 'Mi Empresa (actualizado)',
    'account_host'     => 'smtp.miempresa.com',
    'account_port'     => 465,
    'account_password' => 'nuevapassword',
    'user_id'          => 1,
]);

// Eliminar cuenta
$deleted = Services::deleteAccount(5);
```

### Manager

```php
// Enviar correo por plantilla
$sent = Services::sendByTemplate([
    'template_key'    => 'tpl_1234567890_ab12',
    'recipient_email' => 'usuario@example.com',
    'recipient_name'  => 'Juan Pérez',
    'email_params'    => ['name' => 'Juan', 'code' => '998877'],
]);

// Crear cuenta + template en un solo paso
$instance = Services::createInstance([
    'account_email'        => 'noreply@miempresa.com',
    'account_name'         => 'Mi Empresa',
    'account_host'         => 'smtp.miempresa.com',
    'account_port'         => 587,
    'account_password'     => 'password123',
    'template_html'        => '<h1>Hola {{name}}</h1>',
    'template_name'        => 'Bienvenida',
    'template_description' => 'Correo de bienvenida',
    'template_subject'     => 'Bienvenido',
    'template_params'      => 'name',
    'app_id'               => 'SOM-XXXX',
    'user_id'              => 1,
]);
```

## Configuración

La configuración se gestiona en `sdk_config.json` (creado por `bin/install.php`, **no versionar**).

Estructura:

```json
{
  "active_environment": "prod",
  "environments": {
    "dev":  { "api_key": "" },
    "stg":  { "api_key": "" },
    "prod": { "api_key": "" }
  }
}
```

Copia `sdk_config.json.example` como punto de partida si prefieres configurarlo manualmente.

## CLI

```bash
# Instalador interactivo (crea sdk_config.json)
php vendor/bin/install.php

# Modo no interactivo
php vendor/bin/install.php --api-key="TU_API_KEY" --env=prod
```

## TLS

La verificación TLS está habilitada por defecto. Para desarrollo puedes desactivarla por llamada:

```php
Services::sendByTemplate($payload, null, false);  // tercer parámetro: $verifyTLS
```

## Compatibilidad

- PHP: 8.1+
- Sistemas: Windows, Linux, macOS

## Licencia

MIT (ver `composer.json`).
