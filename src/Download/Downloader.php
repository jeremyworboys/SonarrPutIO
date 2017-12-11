<?php

namespace JeremyWorboys\SonarrPutIO\Download;

use PutIO\API;

class Downloader
{
    /** @var \PutIO\API */
    private $putio;

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
        $this->root = $root;
    }

    /**
     */
    public function run()
    {
        foreach ($this->putio->files->listall() as $item) {
            $this->download($this->root, $item['id']);
        }
    }

    /**
     * @param string $path
     * @param int    $id
     */
    private function download(string $path, int $id)
    {
        $item = $this->putio->files->info($id);

        if ($item['content_type'] === 'text/plain') {
            return;
        } elseif ($item['content_type'] === 'application/x-directory') {
            $path .= '/' . $item['name'];
            $this->downloadDirectory($path, $id);
        } else {
            $this->downloadFile($path, $id);
        }
    }

    /**
     * @param string $path
     * @param int    $id
     */
    private function downloadFile(string $path, int $id)
    {
        $downloaderPath = __DIR__ . '/../../download/download-' . $id . '.php';
        if (file_exists($downloaderPath)) {
            return;
        }

        $downloaderTemplate = file_get_contents(__DIR__ . '/template.txt');
        $downloaderTemplate = strtr($downloaderTemplate, [
            '{{id}}'        => $id,
            '{{path}}'      => $path,
            '{{download}}'  => $this->putio->files->getDownloadURL($id),
        ]);

        $logPath = __DIR__ . '/../../logs/download-' . $id . '.log';

        file_put_contents($downloaderPath, $downloaderTemplate);
        exec("bash -c \"exec nohup php '{$downloaderPath}' >'{$logPath}' 2>&1 &\"");
    }

    /**
     * @param string $path
     * @param int    $id
     */
    private function downloadDirectory(string $path, int $id)
    {
        $files = $this->putio->files->listall($id);

        if (count($files) === 0) {
            $this->putio->files->delete($id);
            return;
        }

        foreach ($files as $item) {
            $this->download($path, $item['id']);
        }
    }
}
