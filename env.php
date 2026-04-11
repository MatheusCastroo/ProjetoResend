<?php

declare(strict_types=1);

/**
 * Carrega variáveis de .env sem sobrescrever as já definidas no servidor (Docker, Apache, etc.).
 */
$envFile = __DIR__ . '/.env';
if (!is_file($envFile) || !is_readable($envFile)) {
    return;
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    return;
}

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    if (!str_contains($line, '=')) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);
    if ($key === '') {
        continue;
    }
    if (getenv($key) !== false) {
        continue;
    }
    if (
        strlen($value) >= 2
        && (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        )
    ) {
        $value = substr($value, 1, -1);
    }
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
}
