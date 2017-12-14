<?php

use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
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

$app->share('putio', function (Container $app) {
    $token = $app->get('config.putio_token');
    $putio = new PutIO\API($token);
    $putio->setSSLVerifyPeer(false);
    return $putio;
});

return $app;
