<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters;
use Psr\Log\LoggerInterface;

class SonarrDownloadHandler
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \JeremyWorboys\SonarrPutIO\DownloadHandler */
    private $handler;

    /**
     * SonarrDownloadHandler constructor.
     *
     * @param \Psr\Log\LoggerInterface                   $logger
     * @param \JeremyWorboys\SonarrPutIO\DownloadHandler $handler
     */
    public function __construct(LoggerInterface $logger, DownloadHandler $handler)
    {
        $this->logger = $logger;
        $this->handler = $handler;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters $params
     */
    public function handleRequest(DownloadParameters $params)
    {
        $filename = $params->getEpisodeFileSourcePath();
        $this->handler->markComplete($filename);
    }
}
