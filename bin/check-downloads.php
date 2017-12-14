#!/usr/bin/env php
<?php

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \JeremyWorboys\SonarrPutIO\DownloadHandler $handler */
$handler = $app->get('download_handler');
$handler->run();
