<?php

namespace JeremyWorboys\SonarrPutIO\Download;

use PutIO\API;

class Downloader
{
    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Download\LinkFinder */
    private $finder;

    /** @var string */
    private $root;

    /**
     * Downloader constructor.
     *
     * @param \PutIO\API $putio
     * @param string     $root
     */
    public function __construct(API $putio, string $root)
    {
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
            foreach ($uploaded as $item) {
                if ($item['name'] === $name) {
                    $this->download($this->root, $item['id']);
                    unset($expected[$i]);
                    $this->writeTransfersList($expected);
                }
            }
        }
    }

    /**
     * @param string $path
     * @param int    $id
     */
    private function download(string $path, int $id)
    {
        $links = $this->finder->getDownloadLinks($id, true);

        $downloaderPath = __DIR__ . '/../../download/download-' . $id . '.php';
        if (file_exists($downloaderPath)) {
            return;
        }

        $downloaderTemplate = file_get_contents(__DIR__ . '/template.txt');
        $downloaderTemplate = strtr($downloaderTemplate, [
            '{{id}}'    => $id,
            '{{path}}'  => $path,
            '{{links}}' => json_encode($links),
        ]);

        $logPath = __DIR__ . '/../../logs/download-' . $id . '.log';

        file_put_contents($downloaderPath, $downloaderTemplate);
        exec("bash -c \"exec nohup php '{$downloaderPath}' >'{$logPath}' 2>&1 &\"");
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
