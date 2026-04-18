<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '31.97.94.243';
$port = getenv('DB_PORT');
$port = ($port !== false && $port !== '') ? (int) $port : 3306;
$name = getenv('DB_NAME') ?: 'default';
$user = getenv('DB_USER') ?: 'mysql';
$pass = getenv('DB_PASS');
$pass = $pass !== false ? $pass : 'nIzMlyTOTfveMMcxZdHHddf4XlVOSCKrbXizdNX5NdtJYixdonFzg3ghXc2vgPHe';

for ($i = 0; $i < 10; $i++) {
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
