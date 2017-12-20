<?php

use JeremyWorboys\SonarrPutIO\DownloadHandler;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\MySQL\MySQLDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\MySQL\MySQLTransferRepository;
use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
use JeremyWorboys\SonarrPutIO\SonarrDownloadHandler;
use JeremyWorboys\SonarrPutIO\SonarrGrabHandler;
use JeremyWorboys\SonarrPutIO\SonarrHandler;
use League\Container\Container;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/config.php';
if (file_exists(__DIR__ . '/config.dist.php')) {
    $config = array_replace($config, require __DIR__ . '/config.dist.php');
}

$app = new Container();
$app->add('config', $config);
foreach ($config as $key => $value) {
    $app->add('config.' . $key, $value);
}

$app->share('logger.psr3_processor', function () {
    return new PsrLogMessageProcessor();
});

$app->share('logger.nested_handler', function () use ($app) {
    $filename = $app->get('config.logs_directory') . '/' . date('Y-m-d') . '.log';
    $processor = $app->get('logger.psr3_processor');

    $handler = new StreamHandler($filename, Logger::DEBUG);
    $handler->pushProcessor($processor);
    return $handler;
});

$app->share('logger.main_handler', function () use ($app) {
    $nested = $app->get('logger.nested_handler');
    $processor = $app->get('logger.psr3_processor');

    $handler = new FingersCrossedHandler($nested, Logger::ERROR);
    $handler->pushProcessor($processor);
    return $handler;
});

$app->share('logger', function () use ($app) {
    $logger = new Logger('app');
    $logger->useMicrosecondTimestamps(true);
    $logger->pushHandler($app->get('logger.main_handler'));
    return $logger;
});

$app->share('macpsd', function () {
    return new ProgressiveDownloader();
});

$app->share('putio', function () use ($app) {
    $token = $app->get('config.putio_token');
    $putio = new PutIO\API($token);
    $putio->setSSLVerifyPeer(false);
    return $putio;
});

$app->share('mysql.connection', function () use ($app) {
    $dsn = $app->get('config.database_dsn');
    $username = $app->get('config.database_username');
    $password = $app->get('config.database_password');

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false,
    ]);
});

$app->share('mysql.download_repository', function () use ($app) {
    $conn = $app->get('mysql.connection');
    return new MySQLDownloadRepository($conn);
});

$app->share('mysql.transfer_repository', function () use ($app) {
    $conn = $app->get('mysql.connection');
    return new MySQLTransferRepository($conn);
});

$app->share('filesystem.download_repository', function () use ($app) {
    $filename = $app->get('config.database_directory') . '/downloads.txt';
    return new FlatFileDownloadRepository($filename);
});

$app->share('filesystem.transfer_repository', function () use ($app) {
    $filename = $app->get('config.database_directory') . '/transfers.txt';
    return new FlatFileTransferRepository($filename);
});

$app->share('download_repository', function () use ($app) {
    $alias = $app->get('config.database_driver') . '.download_repository';
    return $app->get($alias);
});

$app->share('transfer_repository', function () use ($app) {
    $alias = $app->get('config.database_driver') . '.transfer_repository';
    return $app->get($alias);
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
