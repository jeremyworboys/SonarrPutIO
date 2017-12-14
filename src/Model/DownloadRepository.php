<?php

namespace JeremyWorboys\SonarrPutIO\Model;

interface DownloadRepository
{
    /**
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function all(): array;

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function get(int $id): ?Download;

    /**
     * @param int $parentId
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function getByParent(int $parentId): array;

    /**
     * @param string $filename
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function getByFilename(string $filename): ?Download;

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function add(Download $download);

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function remove(Download $download);
}
