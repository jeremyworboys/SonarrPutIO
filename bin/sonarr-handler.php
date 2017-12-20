#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Service\Sonarr\Parameters;

$app = require __DIR__ . '/../bootstrap/app.php';

$logData = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'sonarr_') === 0) {
        $logData[$key] = $value;
    }
}
/** @var \Monolog\Logger $logger */
$logger = $app->get('logger');
$logger->debug('Received Sonarr event.', $logData);

/** @var \JeremyWorboys\SonarrPutIO\SonarrHandler $handler */
$handler = $app->get('sonarr_handler');
$handler->handleRequest(Parameters::createFromServer());
