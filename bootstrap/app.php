<?php

use JeremyWorboys\SonarrPutIO\DependencyInjection as DI;
use League\Container\Container;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Container();
$app->addServiceProvider(new DI\ConfigServiceProvider(__DIR__));
$app->addServiceProvider(new DI\LoggerServiceProvider());
$app->addServiceProvider(new DI\MacPsdServiceProvider());
$app->addServiceProvider(new DI\PutIOServiceProvider());
$app->addServiceProvider(new DI\DatabaseServiceProvider());
$app->addServiceProvider(new DI\ApplicationServiceProvider());

return $app;
