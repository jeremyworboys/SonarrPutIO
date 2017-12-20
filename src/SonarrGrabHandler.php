<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;
use JeremyWorboys\SonarrPutIO\Service\PutIO\TorrentUploader;
use JeremyWorboys\SonarrPutIO\Service\Sonarr\GrabParameters;
use Psr\Log\LoggerInterface;
use PutIO\API;

class SonarrGrabHandler
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Service\PutIO\TorrentUploader */
    private $uploader;

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /**
     * SonarrGrabHandler constructor.
     *
     * @param \Psr\Log\LoggerInterface                            $logger
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository $transfers
     */
    public function __construct(LoggerInterface $logger, API $putio, TransferRepository $transfers)
    {
        $this->logger = $logger;
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
            $this->logger->info('Checking for {type} with filename "{filename}".', [
                'type'     => $type,
                'filename' => $filename,
            ]);

            if (file_exists($filename)) {
                switch ($type) {
                    case 'magnet':
                        $this->logger->info('Found magnet file.');
                        $this->handleMagnetFile($filename);
                        return;

                    case 'torrent':
                        $this->logger->info('Found torrent file.');
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
