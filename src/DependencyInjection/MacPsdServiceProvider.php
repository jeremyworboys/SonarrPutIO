<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
use League\Container\ServiceProvider\AbstractServiceProvider;

class MacPsdServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    protected $provides = [
        'macpsd',
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
        $this->container->share('macpsd', function () {
            return new ProgressiveDownloader();
        });
    }
}
