<?php
/**
 * Copie para config.php e ajuste credenciais.
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
    'resend' => [
        'api_key' => 're_xxxxxxxx',
        'from' => 'Onboarding <onboarding@resend.dev>',
    ],
    'caw_upload' => [
        'api_key' => 'seu_token_caw',
        'url' => 'https://cloud.caw.agency/api/upload',
        'provider' => 'cloudinary',
    ],
];
