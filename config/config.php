<?php

/**
 * Upload de imagens (logo/media) usa caw_upload.* — independente do Resend por application.
 *
 * Prioridade da chave:
 * 1) variável de ambiente CAW_UPLOAD_API_KEY
 * 2) variável de ambiente CAW_API_KEY (retrocompat)
 * 3) valor em 'api_key' abaixo (apenas desenvolvimento local — prefira .env em produção)
 */
$dbHost = getenv('DB_HOST');
if ($dbHost === false || $dbHost === '') {
    $dbHost = '127.0.0.1';
}
$dbPort = getenv('DB_PORT');
if ($dbPort === false || $dbPort === '') {
    $dbPort = '3306';
}
$dbName = getenv('DB_NAME');
if ($dbName === false || $dbName === '') {
    $dbName = 'projeto_resend';
}
$dbUser = getenv('DB_USER');
if ($dbUser === false || $dbUser === '') {
    $dbUser = 'root';
}
$dbPass = getenv('DB_PASS');
if ($dbPass === false) {
    $dbPass = '';
}

$appBaseUrl = getenv('APP_BASE_URL');
if ($appBaseUrl === false) {
    $appBaseUrl = '/ProjetoResend/public';
}

$defaultTestEmail = getenv('DEFAULT_TEST_EMAIL');
if ($defaultTestEmail === false || $defaultTestEmail === '') {
    $defaultTestEmail = 'matheusesteves1160@gmail.com';
}

return [
    'db' => [
        'host' => $dbHost,
        'port' => (int) $dbPort,
        'name' => $dbName,
        'user' => $dbUser,
        'pass' => $dbPass,
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => $appBaseUrl,
        'default_test_email' => $defaultTestEmail,
    ],
    'caw_upload' => [
        'api_key' => getenv('CAW_UPLOAD_API_KEY') ?: getenv('CAW_API_KEY') ?: 'msu_TY1lr9MjktujvWFKP9pEPDN0wUUBwNnG',
        'api_url' => getenv('CAW_UPLOAD_API_URL') ?: 'https://cloud.caw.agency/api/upload',
        'api_base' => getenv('CAW_UPLOAD_API_BASE') ?: 'https://cloud.caw.agency/api',
        'provider' => getenv('CAW_UPLOAD_PROVIDER') ?: 'cloudinary',
        // Retrocompat: mesmo valor que api_url
        'url' => getenv('CAW_UPLOAD_API_URL') ?: 'https://cloud.caw.agency/api/upload',
    ],
];
