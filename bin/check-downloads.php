#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Downloader;

$app = require __DIR__ . '/../bootstrap/app.php';

$putio = $app->get('putio');
$macPsd = $app->get('macpsd');
$downloads = $app->get('download_repository');
$transfers = $app->get('transfer_repository');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($putio, $macPsd, $downloads, $transfers, $root);
$downloader->run();
