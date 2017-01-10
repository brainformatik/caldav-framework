<?php

if (file_exists('vendor/autoload.php')) {
    require_once('vendor/autoload.php');
}

spl_autoload_register(function ($className) {
    $prefix  = 'Brainformatik\\CalDAV\\';
    $baseDir = __DIR__ . '/src/';

    $length = strlen($prefix);
    if (strncmp($prefix, $className, $length) !== 0) {
        return;
    }

    $file = $baseDir . str_replace('\\', '/', substr($className, $length)) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});