<?php
/**
 * Copie para config.php e ajuste credenciais.
 *
 * Upload Caw (logo/media), opcional via ambiente:
 *   CAW_UPLOAD_API_KEY
 *   CAW_UPLOAD_API_URL   (default: https://cloud.caw.agency/api/upload)
 *   CAW_UPLOAD_API_BASE  (default: https://cloud.caw.agency/api)
 *   CAW_UPLOAD_PROVIDER  (default: cloudinary)
 *
 * Docker / ambiente (sobrescrevem os padrões acima quando definidos):
 *   DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_BASE_URL, DEFAULT_TEST_EMAIL
 */
$dbHost = getenv('DB_HOST');
if ($dbHost === false || $dbHost === '') {
    $dbHost = '127.0.0.1';
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
    $defaultTestEmail = 'destinatario@exemplo.com';
}

return [
    'db' => [
        'host' => $dbHost,
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
        'api_key' => getenv('CAW_UPLOAD_API_KEY') ?: getenv('CAW_API_KEY') ?: '',
        'api_url' => 'https://cloud.caw.agency/api/upload',
        'api_base' => 'https://cloud.caw.agency/api',
        'provider' => 'cloudinary',
        'url' => 'https://cloud.caw.agency/api/upload',
    ],
];
