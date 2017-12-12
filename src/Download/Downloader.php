<?php

namespace JeremyWorboys\SonarrPutIO\Download;

use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;
use PutIO\API;

class Downloader
{
    /** @var \JeremyWorboys\SonarrPutIO\ProgressiveDownloader */
    private $psd;

    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Download\LinkFinder */
    private $finder;

    /**
     * Downloader constructor.
     *
     * @param \JeremyWorboys\SonarrPutIO\ProgressiveDownloader $psd
     * @param \PutIO\API                                       $putio
     */
    public function __construct(ProgressiveDownloader $psd, API $putio)
    {
        $this->psd = $psd;
        $this->putio = $putio;
        $this->finder = new LinkFinder($putio);
    }

    /**
     */
    public function run()
    {
        $expected = $this->readTransfersList();
        $uploaded = $this->putio->files->listall();

        foreach ($expected as $i => $name) {
            foreach ($uploaded as $item) {
                if ($item['name'] === $name) {
                    $this->download($item['id']);
                    unset($expected[$i]);
                    $this->writeTransfersList($expected);
                }
            }
        }
    }

    /**
     * @param int $id
     */
    private function download(int $id)
    {
        $links = $this->finder->getDownloadLinks($id, true);

        $this->psd->launchApp();
        foreach ($links as $link) {
            $this->psd->addTask($link);
        }
    }

    /**
     * @return array
     */
    private function readTransfersList()
    {
        $contents = file_get_contents(__DIR__ . '/../../transfers.txt');
        $lines = explode("\n", $contents);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return $lines;
    }

    /**
     * @param array $transfers
     */
    private function writeTransfersList(array $transfers)
    {
        $contents = implode("\n", $transfers);
        file_put_contents(__DIR__ . '/../../transfers.txt', $contents);
    }
}
