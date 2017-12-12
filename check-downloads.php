#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Download\Downloader;
use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;

require_once __DIR__ . '/vendor/autoload.php';

$psd = new ProgressiveDownloader();

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$root = '/Users/jeremyworboys/Downloads/Media';
$downloader = new Downloader($psd, $putio, $root);
$downloader->run();
