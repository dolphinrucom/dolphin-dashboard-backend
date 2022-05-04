<?php

use Jobs\Jira;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$jiraJob = new Jira();
$jiraJob->run();
