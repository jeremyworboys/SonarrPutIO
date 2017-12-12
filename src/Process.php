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
                        $this->handleMagnetGrabRequest($filename);
                        return;

                    case 'torrent':
                        $this->handleTorrentGrabRequest($filename);
                        return;

                    default:
                        throw new \LogicException('Unrecognised file type "' . $type . '".');
                }
            }
        }

        throw new \LogicException('Unable to locate grabbed file "' . $params->getReleaseTitle() . '".');
    }

    /**
     * @param string $filename
     */
    private function handleMagnetGrabRequest(string $filename)
    {
        $magnet = file_get_contents($filename);
        $transfer = $this->putio->transfers->add($magnet);
        $this->appendTransferToList($transfer['name']);
    }

    /**
     * @param string $filename
     */
    private function handleTorrentGrabRequest(string $filename)
    {
        $transfer = $this->putio->files->upload($filename);
        $this->appendTransferToList($transfer['name']);
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
     * @param string $transferName
     */
    private function appendTransferToList(string $transferName)
    {
        file_put_contents(__DIR__ . '/../transfers.txt', $transferName . PHP_EOL, FILE_APPEND);
    }
}
