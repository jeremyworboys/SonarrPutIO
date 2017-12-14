<?php

use JeremyWorboys\SonarrPutIO\Downloader;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;

$app = require __DIR__ . '/../bootstrap/app.php';

$macPsd = $app->get('macpsd');

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$downloads = new FlatFileDownloadRepository(__DIR__ . '/../var/downloads.txt');
$transfers = new FlatFileTransferRepository(__DIR__ . '/../var/transfers.txt');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($putio, $macPsd, $downloads, $transfers, $root);
$downloader->runOnce($_POST['id']);
