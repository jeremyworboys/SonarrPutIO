<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Events\DownloadParameters;
use JeremyWorboys\SonarrPutIO\Events\GrabParameters;
use JeremyWorboys\SonarrPutIO\Events\Parameters;
use JeremyWorboys\SonarrPutIO\Events\RenameParameters;
use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;
use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;
use PutIO\API;

class Process
{
    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\TorrentUploader */
    private $uploader;

    /** @var \JeremyWorboys\SonarrPutIO\Model\DownloadRepository */
    private $downloads;

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /**
     * Process constructor.
     *
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\DownloadRepository $downloads
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository $transfers
     */
    public function __construct(API $putio, DownloadRepository $downloads, TransferRepository $transfers)
    {
        $this->putio = $putio;
        $this->uploader = new TorrentUploader($putio);
        $this->downloads = $downloads;
        $this->transfers = $transfers;
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
        $this->appendTransferToList($transfer['transfer']);
    }

    /**
     * @param string $filename
     */
    private function handleTorrentGrabRequest(string $filename)
    {
        $transfer = $this->uploader->uploadTorrentFile($filename);
        $this->appendTransferToList($transfer);
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\DownloadParameters $params
     */
    private function handleDownloadRequest(DownloadParameters $params)
    {
        $filename = $params->getEpisodeFileSourcePath();
        $download = $this->downloads->getByFilename($filename);

        if ($download) {
            $this->downloads->remove($download);

            $parentId = $download->getParentId();
            $remaining = $this->downloads->getByParent($parentId);
            if (count($remaining) === 0) {
                $this->putio->files->delete($parentId);
            }
        }
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Events\RenameParameters $params
     */
    private function handleRenameRequest(RenameParameters $params) { }

    /**
     * @param array $info
     */
    private function appendTransferToList(array $info)
    {
        $transfer = new Transfer($info['id'], $info['name']);
        $this->transfers->add($transfer);
    }
}
