<?php

/**
 * Minimal PSR-4 autoloader for the App\ namespace, plus a hand-vendored
 * copy of PHPMailer (downloaded directly from GitHub releases since this
 * environment doesn't have Composer/Packagist access). If you later run
 * a real `composer require phpmailer/phpmailer`, Composer will regenerate
 * this file properly and this manual mapping becomes unnecessary.
 */

spl_autoload_register(function (string $class): void {
    $map = [
        'App\\'                  => __DIR__ . '/../app/',
        'PHPMailer\\PHPMailer\\' => __DIR__ . '/phpmailer/phpmailer/src/',
    ];

    foreach ($map as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
