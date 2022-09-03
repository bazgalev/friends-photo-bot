<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Console;
use App\Di;

$config = require_once __DIR__ . '/../config/app.php';

$di = Di::getInstance($config);

try {
    $app = new Console($di);
    $app->execute();
} catch (\Throwable $e) {
    $di->logger()->emergency($e->getMessage(), [
        'exception' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    throw $e;
}
