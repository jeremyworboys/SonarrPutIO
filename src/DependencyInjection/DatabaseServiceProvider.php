<?php

namespace JeremyWorboys\SonarrPutIO\DependencyInjection;

use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile\FlatFileTransferRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\MySQL\MySQLDownloadRepository;
use JeremyWorboys\SonarrPutIO\Infrastructure\MySQL\MySQLTransferRepository;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PDO;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /** @var array */
    protected $provides = [
        'download_repository',
        'transfer_repository',
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
        $this->registerMySQL();
        $this->registerFilesystem();

        $this->container->share('download_repository', function () {
            $alias = $this->container->get('config.database_driver') . '.download_repository';
            return $this->container->get($alias);
        });

        $this->container->share('transfer_repository', function () {
            $alias = $this->container->get('config.database_driver') . '.transfer_repository';
            return $this->container->get($alias);
        });
    }

    private function registerMySQL(): void
    {
        $this->container->share('mysql.connection', function () {
            $dsn = $this->container->get('config.database_dsn');
            $username = $this->container->get('config.database_username');
            $password = $this->container->get('config.database_password');

            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false,
            ]);
        });

        $this->container->share('mysql.download_repository', function () {
            $conn = $this->container->get('mysql.connection');
            return new MySQLDownloadRepository($conn);
        });

        $this->container->share('mysql.transfer_repository', function () {
            $conn = $this->container->get('mysql.connection');
            return new MySQLTransferRepository($conn);
        });
    }

    private function registerFilesystem(): void
    {
        $this->container->share('filesystem.download_repository', function () {
            $filename = $this->container->get('config.database_directory') . '/downloads.txt';
            return new FlatFileDownloadRepository($filename);
        });

        $this->container->share('filesystem.transfer_repository', function () {
            $filename = $this->container->get('config.database_directory') . '/transfers.txt';
            return new FlatFileTransferRepository($filename);
        });
    }
}
