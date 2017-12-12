<?php

namespace JeremyWorboys\SonarrPutIO\Download;

use JeremyWorboys\SonarrPutIO\ProgressiveDownloader;
use PutIO\API;

class Downloader
{
    private const MIN_FILE_SIZE = 100000000; // 100 MB

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
        $remove = [];
        $expected = $this->readTransfersList();
        $uploaded = $this->putio->files->listall();

        foreach ($expected as $i => $name) {
            foreach ($uploaded as $file) {
                if ($file['name'] === $name) {
                    $remove[] = $i;
                    $this->download($file);
                }
            }
        }

        foreach ($remove as $i) {
            unset($expected[$i]);
        }

        $this->writeTransfersList($expected);
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
                if ($file['size'] > self::MIN_FILE_SIZE) {
                    $this->psd->addTask($link);
                    $this->appendDownloadList((int) $parent['id'], $file);
                }
            }
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
