<?php

namespace JeremyWorboys\SonarrPutIO\Download;

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

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /** @var string */
    private $root;

    /**
     * Downloader constructor.
     *
     * @param \JeremyWorboys\SonarrPutIO\ProgressiveDownloader    $psd
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository $transfers
     * @param string                                              $root
     */
    public function __construct(ProgressiveDownloader $psd, API $putio, TransferRepository $transfers, string $root)
    {
        $this->psd = $psd;
        $this->putio = $putio;
        $this->finder = new LinkFinder($putio);
        $this->transfers = $transfers;
        $this->root = $root;
    }

    /**
     */
    public function run()
    {
        foreach ($this->transfers->all() as $transfer) {
            $info = $this->putio->transfers->info($transfer->getId());
            if ($info['status'] === 'SEEDING') {
                $this->putio->transfers->cancel($transfer->getId());
            }
            if ($info['status'] === 'SEEDING' || $info['status'] === 'COMPLETED') {
                $this->download($info['file_id']);
                $this->transfers->remove($transfer);
            }
        }
    }

    /**
     * @param array $parent
     */
    private function download(array $parent)
    {
        $links = $this->finder->getDownloadLinks($parent['id'], true);

        $this->psd->launchApp();
        foreach ($links as $link) {
            if (preg_match('~/files/(\d+)/download~', $link, $matches)) {
                $file = $this->putio->files->info($matches[1]);

                if ($file['size'] < self::MIN_FILE_SIZE) {
                    echo 'Skipping ' . $file['name'] . ' due to filesize.' . PHP_EOL;
                    continue;
                }

                $this->psd->addTask($link);
                $this->appendDownloadList((int) $parent['id'], $file);
            }
        }
    }

    /**
     * @param int   $parentId
     * @param array $file
     */
    private function appendDownloadList(int $parentId, array $file)
    {
        $path = $this->root . '/' . $file['name'];

        $contents = $parentId . "\t" . $path . PHP_EOL;
        file_put_contents(__DIR__ . '/../../downloads.txt', $contents, FILE_APPEND);
    }
}
