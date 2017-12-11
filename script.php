#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Events\Parameters;
use JeremyWorboys\SonarrPutIO\Process;

$logFile = __DIR__ . '/logs/log-' . time() . '.json';
$logData = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'sonarr_') === 0) {
        $logData[$key] = $value;
    }
}
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

require_once __DIR__ . '/vendor/autoload.php';

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$params = Parameters::createFromServer();

$process = new Process($putio);
$process->handleRequest($params);
