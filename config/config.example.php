<?php
/**
 * Copie para config.php e ajuste credenciais.
 *
 * Upload Caw (logo/media), opcional via ambiente:
 *   CAW_UPLOAD_API_KEY
 *   CAW_UPLOAD_API_URL   (default: https://cloud.caw.agency/api/upload)
 *   CAW_UPLOAD_API_BASE  (default: https://cloud.caw.agency/api)
 *   CAW_UPLOAD_PROVIDER  (default: cloudinary)
 */
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'projeto_resend',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '/ProjetoResend/public',
        'default_test_email' => 'destinatario@exemplo.com',
    ],
    'caw_upload' => [
        'api_key' => getenv('CAW_UPLOAD_API_KEY') ?: getenv('CAW_API_KEY') ?: '',
        'api_url' => 'https://cloud.caw.agency/api/upload',
        'api_base' => 'https://cloud.caw.agency/api',
        'provider' => 'cloudinary',
        'url' => 'https://cloud.caw.agency/api/upload',
    ],
];
