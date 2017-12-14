<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;
use JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters;
use PutIO\API;

class SonarrDownloadHandler
{
    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Model\DownloadRepository */
    private $downloads;

    /**
     * SonarrDownloadHandler constructor.
     *
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\DownloadRepository $downloads
     */
    public function __construct(API $putio, DownloadRepository $downloads)
    {
        $this->putio = $putio;
        $this->downloads = $downloads;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters $params
     */
    public function handleRequest(DownloadParameters $params)
    {
        $filename = $params->getEpisodeFileSourcePath();
        $download = $this->downloads->getByFilename($filename);

        if ($download) {
            $this->downloads->remove($download);

            $parentId = $download->getParentId();
            $remaining = $this->downloads->getByParent($parentId);
            if (count($remaining) === 0) {
                $this->putio->files->delete($parentId);
            }
        }
    }
}
