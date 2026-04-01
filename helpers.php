<?php

function config(?string $key = null)
{
    static $cfg;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/config/config.php';
    }
    if ($key === null) {
        return $cfg;
    }
    $parts = explode('.', $key);
    $v = $cfg;
    foreach ($parts as $p) {
        if (!is_array($v) || !array_key_exists($p, $v)) {
            return null;
        }
        $v = $v[$p];
    }
    return $v;
}

function asset_url(string $path): string
{
    return base_url(ltrim($path, '/'));
}

function base_url(string $path = ''): string
{
    $base = rtrim((string) config('app.base_url'), '/');
    $path = ltrim($path, '/');
    return $base . ($path !== '' ? '/' . $path : '');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(string $key, ?string $message = null): mixed
{
    if (!isset($_SESSION)) {
        session_start();
    }
    if ($message === null) {
        $m = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $m;
    }
    $_SESSION['_flash'][$key] = $message;
    return null;
}

function csrf_token(): string
{
    if (!isset($_SESSION)) {
        session_start();
    }
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_verify(): void
{
    if (!isset($_SESSION)) {
        session_start();
    }
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['_csrf'] ?? '', $t)) {
        http_response_code(403);
        exit('CSRF inválido');
    }
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Badge HTML para status de e-mail (ENVIADO / ERRO / PENDENTE). */
function badge_status(string $status): string
{
    $s = strtoupper(trim($status));
    $class = 'badge-saas badge-saas--neutral';
    if ($s === 'ENVIADO') {
        $class = 'badge-saas badge-saas--ok';
    } elseif ($s === 'ERRO') {
        $class = 'badge-saas badge-saas--err';
    } elseif ($s === 'PENDENTE') {
        $class = 'badge-saas badge-saas--pend';
    }
    return '<span class="' . e($class) . '">' . e($s) . '</span>';
}

/**
 * Caminho da requisição relativo ao base_url (ex.: /applications).
 */
function view(string $name, array $data = []): void
{
    $data['title'] = $data['title'] ?? 'Painel';
    extract($data, EXTR_SKIP);
    $viewFile = __DIR__ . '/views/' . $name . '.php';
    if (!is_file($viewFile)) {
        http_response_code(500);
        echo 'View não encontrada: ' . e($name);
        return;
    }
    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/views/layout.php';
}

function request_path(): string
{
    if (isset($_GET['r']) && is_string($_GET['r'])) {
        $p = '/' . trim($_GET['r'], '/');
        return $p === '//' ? '/' : $p;
    }
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = preg_replace('#/index\.php$#i', '/', $path);
    $base = rtrim((string) config('app.base_url'), '/');
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }
    $path = '/' . trim($path, '/');
    return $path === '//' ? '/' : $path;
}
