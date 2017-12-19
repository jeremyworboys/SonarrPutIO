<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;
use JeremyWorboys\SonarrPutIO\Service\PutIO\TorrentUploader;
use JeremyWorboys\SonarrPutIO\Service\Sonarr\GrabParameters;
use PutIO\API;

class SonarrGrabHandler
{
    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Service\PutIO\TorrentUploader */
    private $uploader;

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /**
     * SonarrGrabHandler constructor.
     *
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository $transfers
     */
    public function __construct(API $putio, TransferRepository $transfers)
    {
        $this->putio = $putio;
        $this->uploader = new TorrentUploader($putio);
        $this->transfers = $transfers;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Service\Sonarr\GrabParameters $params
     */
    public function handleRequest(GrabParameters $params)
    {
        $files = [
            'magnet'  => __DIR__ . '/../var/torrents/' . $params->getReleaseTitle() . '.magnet',
            'torrent' => __DIR__ . '/../var/torrents/' . $params->getReleaseTitle() . '.torrent',
        ];

        foreach ($files as $type => $filename) {
            if (file_exists($filename)) {
                switch ($type) {
                    case 'magnet':
                        $this->handleMagnetFile($filename);
                        return;

                    case 'torrent':
                        $this->handleTorrentFile($filename);
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
    private function handleMagnetFile(string $filename)
    {
        $magnet = file_get_contents($filename);
        $transfer = $this->putio->transfers->add($magnet);
        $this->appendTransferToList($transfer['transfer']);
    }

    /**
     * @param string $filename
     */
    private function handleTorrentFile(string $filename)
    {
        $transfer = $this->uploader->uploadTorrentFile($filename);
        $this->appendTransferToList($transfer);
    }

    /**
     * @param array $info
     */
    private function appendTransferToList(array $info)
    {
        $transfer = new Transfer($info['id'], $info['name']);
        $this->transfers->add($transfer);
    }
}
