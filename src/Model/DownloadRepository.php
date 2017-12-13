<?php

namespace JeremyWorboys\SonarrPutIO\Model;

class DownloadRepository
{
    /** @var string */
    private $filename;

    /** @var \JeremyWorboys\SonarrPutIO\Model\Download[] */
    private $downloads;

    /**
     * DownloadRepository constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->loadData();
    }

    /**
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function all(): array
    {
        return $this->downloads;
    }

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function get(int $id): ?Download
    {
        foreach ($this->downloads as $download) {
            if ($download->getId() === $id) {
                return $download;
            }
        }

        return null;
    }

    /**
     * @param int $parentId
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function getByParent(int $parentId): array
    {
        $result = [];
        foreach ($this->downloads as $download) {
            if ($download->getParentId() === $parentId) {
                $result[] = $download;
            }
        }

        return $result;
    }

    /**
     * @param string $filename
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function getByFilename(string $filename): ?Download
    {
        foreach ($this->downloads as $download) {
            if ($download->getFilename() === $filename) {
                return $download;
            }
        }

        return null;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function add(Download $download)
    {
        $this->downloads[] = $download;
        $this->flushData();
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function remove(Download $download)
    {
        $index = array_search($download, $this->downloads, true);

        if ($index !== false) {
            unset($this->downloads[$index]);
            $this->flushData();
        }
    }

    /**
     */
    private function loadData(): void
    {
        $contents = file_get_contents($this->filename);
        $lines = explode(PHP_EOL, $contents);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        $this->downloads = [];
        foreach ($lines as $line) {
            [$id, $parentId, $filename] = explode("\t", $line);
            $this->downloads[] = new Download($id, $parentId, $filename);
        }
    }

    /**
     */
    private function flushData(): void
    {
        $lines = [];
        foreach ($this->downloads as $download) {
            $lines[] = $download->getId() . "\t" . $download->getParentId() . "\t" . $download->getFilename();
        }

        $contents = implode(PHP_EOL, $lines) . PHP_EOL;
        file_put_contents($this->filename, $contents);
    }
}
