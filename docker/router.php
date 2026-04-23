<?php

/**
 * Roteador para o servidor embutido do PHP (`php -S`).
 * - Arquivos reais em `public/` (CSS, etc.) seguem o fluxo padrão (retorna false).
 * - Demais caminhos passam para `public/index.php` (front controller).
 */
declare(strict_types=1);

$public = realpath(__DIR__ . '/../public');
if ($public === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Diretório public/ não encontrado.' . "\n";
    return true;
}

$uri = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
$path = rawurldecode($uri);
if (!str_starts_with($path, '/')) {
    $path = '/' . $path;
}

$full = $public . $path;
$real = $path === '/' || $path === '' ? null : @realpath($full);

if ($path !== '/' && $real && str_starts_with($real, $public) && is_file($real)) {
    chdir($public);
    return false;
}

chdir($public);
require $public . '/index.php';
return true;
