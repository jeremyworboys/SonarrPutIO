#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Downloader;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;

$app = require __DIR__ . '/../bootstrap/app.php';

$macPsd = new ProgressiveDownloader();

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$downloads = new FlatFileDownloadRepository(__DIR__ . '/../var/downloads.txt');
$transfers = new FlatFileTransferRepository(__DIR__ . '/../var/transfers.txt');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($putio, $macPsd, $downloads, $transfers, $root);
$downloader->run();
