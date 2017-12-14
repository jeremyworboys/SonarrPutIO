<?php

use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
use League\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Container();

$app->share('macpsd', function () {
    return new ProgressiveDownloader();
});

return $app;
