#!/usr/bin/env php
<?php

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \JeremyWorboys\SonarrPutIO\Downloader $downloader */
$downloader = $app->get('download_handler');
$downloader->run();
