#!/usr/bin/env php
<?php

use JeremyWorboys\SonarrPutIO\Download\Downloader;

require_once __DIR__ . '/vendor/autoload.php';

$putio = new PutIO\API('***REMOVED***');
$putio->setSSLVerifyPeer(false);

$root = '/Users/jeremyworboys/Downloads/Media';
$downloader = new Downloader($putio, $root);
$downloader->run();
