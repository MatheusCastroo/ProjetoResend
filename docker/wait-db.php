<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT');
$port = ($port !== false && $port !== '') ? (int) $port : 3306;
$name = getenv('DB_NAME') ?: 'projeto_resend';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS');
$pass = $pass !== false ? $pass : '';

for ($i = 0; $i < 60; $i++) {
    try {
        new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $name),
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
