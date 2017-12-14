#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Events\Parameters;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\Process;

$logFile = __DIR__ . '/logs/' . $_SERVER['sonarr_eventtype'] . '-' . time() . '.json';
$logData = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'sonarr_') === 0) {
        $logData[$key] = $value;
    }
}
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

require_once __DIR__ . '/../vendor/autoload.php';

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$downloads = new FlatFileDownloadRepository(__DIR__ . '/../var/downloads.txt');
$transfers = new FlatFileTransferRepository(__DIR__ . '/../var/transfers.txt');

$process = new Process($putio, $downloads, $transfers);
$process->handleRequest(Parameters::createFromServer());
