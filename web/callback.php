<?php

use JeremyWorboys\SonarrPutIO\Downloader;

$logFile = __DIR__ . '/../logs/callback-' . time() . '.json';
file_put_contents($logFile, json_encode($_POST, JSON_PRETTY_PRINT));

$app = require __DIR__ . '/../bootstrap/app.php';

$putio = $app->get('putio');
$macPsd = $app->get('macpsd');
$downloads = $app->get('download_repository');
$transfers = $app->get('transfer_repository');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($putio, $macPsd, $downloads, $transfers, $root);
$downloader->runOnce($_POST['id']);
