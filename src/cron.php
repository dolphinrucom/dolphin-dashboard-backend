<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

foreach ($config['cron']['jobs'] as $job) {
    $jobClass = '\Jobs\\' . ucfirst($job);
    $jobInstance = new $jobClass();

    $jobInstance->run();
}
