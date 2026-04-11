<?php

declare(strict_types=1);

/**
 * CLI: cria tabelas no banco definido em .env / config (bootstrap_tables.sql).
 * Uso: php database/bootstrap_db.php
 */

require_once dirname(__DIR__) . '/env.php';

$c = require dirname(__DIR__) . '/config/config.php';
$db = $c['db'];
$port = $db['port'] ?? 3306;

$pdo = new PDO(
    sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'],
        $port,
        $db['name'],
        $db['charset']
    ),
    $db['user'],
    $db['pass'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]
);

$sql = file_get_contents(__DIR__ . '/bootstrap_tables.sql');
$pdo->exec($sql);

echo "Tabelas verificadas/criadas em {$db['name']} @ {$db['host']}\n";
