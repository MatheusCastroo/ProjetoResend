<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

session_start();

require_once __DIR__ . '/helpers.php';

spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/core/',
        __DIR__ . '/controllers/',
        __DIR__ . '/services/',
        __DIR__ . '/repositories/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
