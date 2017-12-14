#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Service\Sonarr\Parameters;

$logFile = __DIR__ . '/../logs/' . $_SERVER['sonarr_eventtype'] . '-' . time() . '.json';
$logData = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'sonarr_') === 0) {
        $logData[$key] = $value;
    }
}
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \JeremyWorboys\SonarrPutIO\SonarrHandler $handler */
$handler = $app->get('sonarr_handler');
$handler->handleRequest(Parameters::createFromServer());
