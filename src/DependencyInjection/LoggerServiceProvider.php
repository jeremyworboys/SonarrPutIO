<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class LoggerServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    protected $provides = [
        'logger'
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->share('logger.psr3_processor', function () {
            return new PsrLogMessageProcessor();
        });

        $this->container->share('logger.console_handler', function () {
            return new StreamHandler('php://stdout', Logger::DEBUG);
        });

        $this->container->share('logger.nested_handler', function () {
            $filename = $this->container->get('config.logs_directory') . '/' . date('Y-m-d') . '.log';
            $processor = $this->container->get('logger.psr3_processor');

            $handler = new StreamHandler($filename, Logger::DEBUG);
            $handler->pushProcessor($processor);
            return $handler;
        });

        $this->container->share('logger.main_handler', function () {
            $nested = $this->container->get('logger.nested_handler');
            $processor = $this->container->get('logger.psr3_processor');

            $handler = new FingersCrossedHandler($nested, Logger::ERROR);
            $handler->pushProcessor($processor);
            return $handler;
        });

        $this->container->share('logger', function () {
            $logger = new Logger('app');
            $logger->useMicrosecondTimestamps(true);
            $logger->pushHandler($this->container->get('logger.main_handler'));
            $logger->pushHandler($this->container->get('logger.console_handler'));
            return $logger;
        });
    }
}
