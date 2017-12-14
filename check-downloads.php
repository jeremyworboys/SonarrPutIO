#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Download\Downloader;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;

require_once __DIR__ . '/vendor/autoload.php';

$psd = new ProgressiveDownloader();

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$downloads = new FlatFileDownloadRepository(__DIR__ . '/var/downloads.txt');
$transfers = new FlatFileTransferRepository(__DIR__ . '/var/transfers.txt');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($psd, $putio, $downloads, $transfers, $root);
$downloader->run();
