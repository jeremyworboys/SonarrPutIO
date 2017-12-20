<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use JeremyWorboys\SonarrPutIO\DownloadHandler;
use JeremyWorboys\SonarrPutIO\SonarrDownloadHandler;
use JeremyWorboys\SonarrPutIO\SonarrGrabHandler;
use JeremyWorboys\SonarrPutIO\SonarrHandler;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ApplicationServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    protected $provides = [
        'sonarr_handler',
        'download_handler',
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
        $this->container->share('sonarr_handler', function () {
            return new SonarrHandler(
                $this->container->get('sonarr_handler.grab'),
                $this->container->get('sonarr_handler.download')
            );
        });

        $this->container->share('sonarr_handler.grab', function () {
            return new SonarrGrabHandler(
                $this->container->get('logger'),
                $this->container->get('putio'),
                $this->container->get('transfer_repository')
            );
        });

        $this->container->share('sonarr_handler.download', function () {
            return new SonarrDownloadHandler(
                $this->container->get('logger'),
                $this->container->get('putio'),
                $this->container->get('download_repository')
            );
        });

        $this->container->share('download_handler', function () {
            return new DownloadHandler(
                $this->container->get('putio'),
                $this->container->get('macpsd'),
                $this->container->get('download_repository'),
                $this->container->get('transfer_repository'),
                $this->container->get('config.media_directory')
            );
        });
    }
}
