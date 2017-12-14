<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Service\Sonarr\Parameters;

class SonarrHandler
{
    /** @var \JeremyWorboys\SonarrPutIO\SonarrGrabHandler */
    private $grabHandler;

    /** @var \JeremyWorboys\SonarrPutIO\SonarrDownloadHandler */
    private $downloadHandler;

    /**
     * SonarrHandler constructor.
     *
     * @param \JeremyWorboys\SonarrPutIO\SonarrGrabHandler     $grabHandler
     * @param \JeremyWorboys\SonarrPutIO\SonarrDownloadHandler $downloadHandler
     */
    public function __construct(SonarrGrabHandler $grabHandler, SonarrDownloadHandler $downloadHandler)
    {
        $this->grabHandler = $grabHandler;
        $this->downloadHandler = $downloadHandler;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Service\Sonarr\Parameters $params
     */
    public function handleRequest(Parameters $params)
    {
        switch ($params->getEventType()) {
            case 'Grab':
                /** @var \JeremyWorboys\SonarrPutIO\Service\Sonarr\GrabParameters $params */
                $this->grabHandler->handleRequest($params);
                return;

            case 'Download':
                /** @var \JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters $params */
                $this->downloadHandler->handleRequest($params);
                return;

            case 'Rename':
                /** @var \JeremyWorboys\SonarrPutIO\Service\Sonarr\RenameParameters $params */
                // No request handler
                return;

            default:
                throw new \LogicException('Unrecognised event type "' . $params->getEventType() . '".');
        }
    }
}
