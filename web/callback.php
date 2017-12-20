<?php

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Monolog\Logger $logger */
$logger = $app->get('logger');
$logger->debug('Received Put.io callback.', $_POST);

/** @var \JeremyWorboys\SonarrPutIO\DownloadHandler $handler */
$handler = $app->get('download_handler');
$handler->runOnce($_POST['id']);
