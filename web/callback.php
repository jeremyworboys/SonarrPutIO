<?php

$logFile = __DIR__ . '/../var/logs/callback-' . time() . '.json';
file_put_contents($logFile, json_encode($_POST, JSON_PRETTY_PRINT));

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \JeremyWorboys\SonarrPutIO\DownloadHandler $handler */
$handler = $app->get('download_handler');
$handler->runOnce($_POST['id']);
