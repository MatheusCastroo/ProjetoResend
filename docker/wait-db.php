<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'db';
$name = getenv('DB_NAME') ?: 'projeto_resend';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS');
$pass = $pass !== false ? $pass : '';

for ($i = 0; $i < 60; $i++) {
    try {
        new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name),
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        exit(0);
    } catch (Throwable) {
        sleep(1);
    }
}

fwrite(STDERR, "MySQL em {$host} não respondeu a tempo.\n");
exit(1);
