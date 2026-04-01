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
    'caw_upload' => [
        'api_key' => getenv('CAW_API_KEY') ?: '',
        'url' => 'https://cloud.caw.agency/api/upload',
        'provider' => 'cloudinary',
    ],
];
