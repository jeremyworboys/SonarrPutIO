#!/usr/bin/env php
<?php

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Monolog\Logger $logger */
$logger = $app->get('logger');
$logger->debug('Running manual check downloads.');

/** @var \JeremyWorboys\SonarrPutIO\DownloadHandler $handler */
$handler = $app->get('download_handler');
$handler->run();
