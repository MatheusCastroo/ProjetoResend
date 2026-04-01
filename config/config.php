<?php
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
        // Preferir variável de ambiente RESEND_API_KEY em produção (não commitar segredo).
        'api_key' => getenv('RESEND_API_KEY') ?: 're_SHmRh5Ti_aKHzLSusmGA38Cq4uFASbmNn',
        'from' => getenv('RESEND_FROM') ?: 'onboarding@resend.dev',
    ],
    'caw_upload' => [
        'api_key' => getenv('CAW_API_KEY') ?: '',
        'url' => 'https://cloud.caw.agency/api/upload',
        'provider' => 'cloudinary',
    ],
];
