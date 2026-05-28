#!/usr/bin/env php
<?php
$autoloadCandidates = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    getcwd() . '/vendor/autoload.php',
];
$loaded = false;
foreach ($autoloadCandidates as $candidate) {
    if (is_file($candidate)) { require $candidate; $loaded = true; break; }
}
if (!$loaded || !class_exists('SoftanMailing\\SDK')) {
    fwrite(STDERR, "No se encontro el autoloader de Composer.\n");
    exit(1);
}

use SoftanMailing\SDK;
use SoftanMailing\Config;
use SoftanMailing\Services;

SDK::init();

echo "Softan Mailing PHP SDK — Instalador\n\n";

function prompt(string $label, bool $hidden = false): string {
    if ($hidden && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        echo $label;
        @system('stty -echo');
        $val = rtrim(fgets(STDIN));
        @system('stty echo');
        echo "\n";
        return $val;
    }
    echo $label;
    return rtrim(fgets(STDIN));
}

$opts      = getopt('', ['api-key::', 'env::']);
$activeEnv = $opts['env'] ?? (SDK::$CONFIG['active_environment'] ?? (SDK::$META['default_environment'] ?? 'prod'));
$apiKey    = $opts['api-key'] ?? prompt("API Key (X-API-KEY) para [{$activeEnv}]: ", true);

$cfg = SDK::$CONFIG ?: ['active_environment' => $activeEnv, 'environments' => []];
$cfg['active_environment']          = $activeEnv;
$cfg['environments'][$activeEnv]    = ['api_key' => $apiKey];

echo "\nValidando credenciales contra {$activeEnv}...\n";
$baseUrl = Config::getMeta('base_urls', [])[$activeEnv] ?? '';
$res     = Services::listTemplates(null, true);

if (isset($res['success']) && $res['success'] === true) {
    if (SDK::saveJson(SDK::CONFIG_PATH, $cfg)) {
        echo "OK — Configuracion guardada en sdk_config.json\n";
        exit(0);
    }
    fwrite(STDERR, "ERROR — No se pudo escribir sdk_config.json\n");
    exit(1);
}

fwrite(STDERR, "ERROR — No fue posible validar credenciales. Respuesta:\n" . json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
exit(1);
