<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Events\DownloadParameters;
use JeremyWorboys\SonarrPutIO\Events\GrabParameters;
use JeremyWorboys\SonarrPutIO\Events\Parameters;
use JeremyWorboys\SonarrPutIO\Events\RenameParameters;
use PutIO\API;

class Process
{
    /** @var \PutIO\API */
    private $putio;

    /**
     * Process constructor.
     *
     * @param \PutIO\API $putio
     */
    public function __construct(API $putio)
    {
        $this->putio = $putio;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\Parameters $params
     */
    public function handleRequest(Parameters $params)
    {
        switch ($params->getEventType()) {
            case 'Grab':
                /** @var \JeremyWorboys\SonarrPutIO\Events\GrabParameters $params */
                $this->handleGrabRequest($params);
                return;

            case 'Download':
                /** @var \JeremyWorboys\SonarrPutIO\Events\DownloadParameters $params */
                $this->handleDownloadRequest($params);
                return;

            case 'Rename':
                /** @var \JeremyWorboys\SonarrPutIO\Events\RenameParameters $params */
                $this->handleRenameRequest($params);
                return;

            default:
                throw new \LogicException('Unrecognised event type "' . $params->getEventType() . '".');
        }
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\GrabParameters $params
     */
    private function handleGrabRequest(GrabParameters $params)
    {
        $files = [
            'magnet'  => __DIR__ . '/../torrents/' . $params->getReleaseTitle() . '.magnet',
            'torrent' => __DIR__ . '/../torrents/' . $params->getReleaseTitle() . '.torrent',
        ];

        foreach ($files as $type => $filename) {
            if (file_exists($filename)) {
                switch ($type) {
                    case 'magnet':
                        $this->handleMagnetGrabRequest($params, $filename);
                        return;

                    case 'torrent':
                        $this->handleTorrentGrabRequest($params, $filename);
                        return;

                    default:
                        throw new \LogicException('Unrecognised file type "' . $type . '".');
                }
            }
        }

        throw new \LogicException('Unable to locate grabbed file "' . $params->getReleaseTitle() . '".');
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\GrabParameters $params
     * @param string                                           $filename
     */
    private function handleMagnetGrabRequest(GrabParameters $params, string $filename)
    {
        $magnet = file_get_contents($filename);
        $this->putio->transfers->add($magnet);
        $this->appendTransferToList($params->getReleaseTitle());
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\GrabParameters $params
     * @param string                                           $filename
     */
    private function handleTorrentGrabRequest(GrabParameters $params, string $filename)
    {
        $this->putio->files->upload($filename);
        $this->appendTransferToList($params->getReleaseTitle());
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\DownloadParameters $params
     */
    private function handleDownloadRequest(DownloadParameters $params) { }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\RenameParameters $params
     */
    private function handleRenameRequest(RenameParameters $params) { }

    /**
     * @param string $filename
     */
    private function appendTransferToList(string $filename)
    {
        file_put_contents(__DIR__ . '/../transfers.txt', $filename . PHP_EOL, FILE_APPEND);
    }
}
