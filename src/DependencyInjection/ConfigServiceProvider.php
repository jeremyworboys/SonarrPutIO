<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ConfigServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    private $config;

    /**
     * ConfigServiceProvider constructor.
     *
     * @param string $configDir
     */
    public function __construct(string $configDir)
    {
        $this->config = $this->loadConfig($configDir);

        $this->provides[] = 'config';
        foreach ($this->config as $key => $value) {
            $this->provides[] = 'config.' . $key;
        }
    }

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->add('config', $this->config);

        foreach ($this->config as $key => $value) {
            $this->container->add('config.' . $key, $value);
        }
    }

    private function loadConfig(string $configDir): array
    {
        $configFile = $configDir . '/config.php';
        $configDist = $configDir . '/config.dist.php';

        $config = require $configFile;

        if (file_exists($configDist)) {
            $config = array_replace($config, require $configDist);
        }

        return $config;
    }
}
