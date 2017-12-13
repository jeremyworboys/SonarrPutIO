<?php

namespace JeremyWorboys\SonarrPutIO\Download;

use JeremyWorboys\SonarrPutIO\Model\Download;
use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;
use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;
use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;
use PutIO\API;

class Downloader
{
    private const MIN_FILE_SIZE = 10000000; // 10 MB

    /** @var \JeremyWorboys\SonarrPutIO\ProgressiveDownloader */
    private $psd;

    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Download\LinkFinder */
    private $finder;

    /** @var \JeremyWorboys\SonarrPutIO\Model\DownloadRepository */
    private $downloads;

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /** @var string */
    private $root;

    /**
     * Downloader constructor.
     *
     * @param \JeremyWorboys\SonarrPutIO\ProgressiveDownloader    $psd
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\DownloadRepository $downloads
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository $transfers
     * @param string                                              $root
     */
    public function __construct(ProgressiveDownloader $psd, API $putio, DownloadRepository $downloads, TransferRepository $transfers, string $root)
    {
        $this->psd = $psd;
        $this->putio = $putio;
        $this->finder = new LinkFinder($putio);
        $this->downloads = $downloads;
        $this->transfers = $transfers;
        $this->root = $root;
    }

    /**
     */
    public function run()
    {
        foreach ($this->transfers->all() as $transfer) {
            $this->handleTransfer($transfer);
        }
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    private function handleTransfer(Transfer $transfer)
    {
        $info = $this->putio->transfers->info($transfer->getId());
        $info = $info['transfer'];

        if ($info['status'] === 'SEEDING') {
            $this->putio->transfers->cancel($transfer->getId());
        }

        if ($info['status'] === 'SEEDING' || $info['status'] === 'COMPLETED') {
            $this->download($info['file_id']);
            $this->transfers->remove($transfer);
        }
    }

    /**
     * @param int $parentId
     */
    private function download(int $parentId)
    {
        $links = $this->finder->getDownloadLinks($parentId, true);

        $this->psd->launchApp();
        foreach ($links as $link) {
            if (preg_match('~/files/(\d+)/download~', $link, $matches)) {
                $file = $this->putio->files->info($matches[1]);

                if ($file['size'] < self::MIN_FILE_SIZE) {
                    echo 'Skipping ' . $file['name'] . ' due to filesize.' . PHP_EOL;
                    continue;
                }

                $this->psd->addTask($link);
                $this->appendDownloadList($parentId, $file);
            }
        }
    }

    /**
     * @param int   $parentId
     * @param array $file
     */
    private function appendDownloadList(int $parentId, array $file)
    {
        $filename = $this->root . '/' . $file['name'];
        $download = new Download($file['id'], $parentId, $filename);
        $this->downloads->add($download);
    }
}
