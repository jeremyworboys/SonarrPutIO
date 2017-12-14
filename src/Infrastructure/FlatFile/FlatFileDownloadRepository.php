<?php

namespace JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile;

use JeremyWorboys\SonarrPutIO\Model\Download;
use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;

class FlatFileDownloadRepository extends FlatFileRepository implements DownloadRepository
{
    /** @var \JeremyWorboys\SonarrPutIO\Model\Download[] */
    private $downloads;

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
        //$this->withWriteLock(function () use ($download) {
        //    $this->loadData();
        $this->downloads[] = $download;
        $this->flushData();
        //});
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function remove(Download $download)
    {
        //$this->withWriteLock(function () use ($download) {
        //    $this->loadData();
        $index = array_search($download, $this->downloads, true);

        if ($index !== false) {
            unset($this->downloads[$index]);
            $this->flushData();
        }
        //});
    }

    /**
     * @param array $lines
     */
    protected function readLines(array $lines): void
    {
        $this->downloads = [];
        foreach ($lines as $line) {
            [$id, $parentId, $filename] = explode("\t", $line);
            $this->downloads[] = new Download($id, $parentId, $filename);
        }
    }

    /**
     * @return array
     */
    protected function writeLines(): array
    {
        $lines = [];
        foreach ($this->downloads as $download) {
            $lines[] = $download->getId() . "\t" . $download->getParentId() . "\t" . $download->getFilename();
        }

        return $lines;
    }
}
