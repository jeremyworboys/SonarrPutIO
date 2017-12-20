<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use League\Container\ServiceProvider\AbstractServiceProvider;
use PutIO\API;

class PutIOServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    protected $provides = [
        'putio',
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
        $this->container->share('putio', function () {
            $token = $this->container->get('config.putio_token');

            $putio = new API($token);
            $putio->setSSLVerifyPeer(false);
            return $putio;
        });
    }
}
