<?php

$logFile = __DIR__ . '/../logs/callback-' . time() . '.json';
file_put_contents($logFile, json_encode($_POST, JSON_PRETTY_PRINT));

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \JeremyWorboys\SonarrPutIO\Downloader $downloader */
$downloader = $app->get('download_handler');
$downloader->runOnce($_POST['id']);
