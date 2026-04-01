<?php

/**
 * Upload de imagens (logo/media) usa caw_upload.* — independente do Resend por application.
 *
 * Prioridade da chave:
 * 1) variável de ambiente CAW_UPLOAD_API_KEY
 * 2) variável de ambiente CAW_API_KEY (retrocompat)
 * 3) valor em 'api_key' abaixo (apenas desenvolvimento local — prefira .env em produção)
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
        'default_test_email' => 'matheusesteves1160@gmail.com',
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
