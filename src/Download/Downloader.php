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

    /** @var string */
    private $root;

    /**
     * Downloader constructor.
     *
     * @param \JeremyWorboys\SonarrPutIO\ProgressiveDownloader $psd
     * @param \PutIO\API                                       $putio
     * @param string                                           $root
     */
    public function __construct(ProgressiveDownloader $psd, API $putio, string $root)
    {
        $this->psd = $psd;
        $this->putio = $putio;
        $this->finder = new LinkFinder($putio);
        $this->root = $root;
    }

    /**
     */
    public function run()
    {
        $expected = $this->readTransfersList();
        $uploaded = $this->putio->files->listall();

        foreach ($expected as $i => $name) {
            foreach ($uploaded as $file) {
                if ($file['name'] === $name) {
                    $this->download($file);
                    unset($expected[$i]);
                    $this->writeTransfersList($expected);
                }
            }
        }
    }

    /**
     * @param array $file
     */
    private function download(array $file)
    {
        $links = $this->finder->getDownloadLinks($file['id'], true);

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
        $lines = explode(PHP_EOL, $contents);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return $lines;
    }

    /**
     * @param array $transfers
     */
    private function writeTransfersList(array $transfers)
    {
        $contents = implode(PHP_EOL, $transfers) . PHP_EOL;
        file_put_contents(__DIR__ . '/../../transfers.txt', $contents);
    }
}
