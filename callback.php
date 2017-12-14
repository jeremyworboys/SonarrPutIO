<?php

use JeremyWorboys\SonarrPutIO\Download\Downloader;
use JeremyWorboys\SonarrPutIO\Model\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Model\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;

require_once __DIR__ . '/vendor/autoload.php';

$psd = new ProgressiveDownloader();

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$downloads = new FlatFileDownloadRepository(__DIR__ . '/downloads.txt');
$transfers = new FlatFileTransferRepository(__DIR__ . '/transfers.txt');

$root = '/Users/jeremyworboys/Downloads/Media';

$downloader = new Downloader($psd, $putio, $downloads, $transfers, $root);
$downloader->runOnce($_POST['id']);
