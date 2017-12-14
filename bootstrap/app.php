<?php

use JeremyWorboys\SonarrPutIO\DownloadHandler;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
use JeremyWorboys\SonarrPutIO\SonarrDownloadHandler;
use JeremyWorboys\SonarrPutIO\SonarrGrabHandler;
use JeremyWorboys\SonarrPutIO\SonarrHandler;
use League\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$app = new Container();
$app->add('config', $config);
foreach ($config as $key => $value) {
    $app->add('config.' . $key, $value);
}

$app->share('macpsd', function () {
    return new ProgressiveDownloader();
});

$app->share('putio', function () use ($app) {
    $token = $app->get('config.putio_token');
    $putio = new PutIO\API($token);
    $putio->setSSLVerifyPeer(false);
    return $putio;
});

$app->share('download_repository', function () use ($app) {
    $database = $app->get('config.database_directory') . '/downloads.txt';
    return new FlatFileDownloadRepository($database);
});

$app->share('transfer_repository', function () use ($app) {
    $database = $app->get('config.database_directory') . '/transfers.txt';
    return new FlatFileTransferRepository($database);
});

$app->share('sonarr_handler', function () use ($app) {
    return new SonarrHandler(
        $app->get('sonarr_handler.grab'),
        $app->get('sonarr_handler.download')
    );
});

$app->share('sonarr_handler.grab', function () use ($app) {
    return new SonarrGrabHandler(
        $app->get('putio'),
        $app->get('transfer_repository')
    );
});

$app->share('sonarr_handler.download', function () use ($app) {
    return new SonarrDownloadHandler(
        $app->get('putio'),
        $app->get('download_repository')
    );
});

$app->share('download_handler', function () use ($app) {
    return new DownloadHandler(
        $app->get('putio'),
        $app->get('macpsd'),
        $app->get('download_repository'),
        $app->get('transfer_repository'),
        $app->get('config.media_directory')
    );
});

return $app;
