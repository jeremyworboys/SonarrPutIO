<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;
use JeremyWorboys\SonarrPutIO\Service\Sonarr\DownloadParameters;
use Psr\Log\LoggerInterface;
use PutIO\API;

class SonarrDownloadHandler
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Model\DownloadRepository */
    private $downloads;

    /**
     * SonarrDownloadHandler constructor.
     *
     * @param \Psr\Log\LoggerInterface                            $logger
     * @param \PutIO\API                                          $putio
     * @param \JeremyWorboys\SonarrPutIO\Model\DownloadRepository $downloads
     */
    public function __construct(LoggerInterface $logger, API $putio, DownloadRepository $downloads)
    {
        $this->logger = $logger;
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
            $this->logger->info('Found download with filename "{filename}".', ['filename' => $filename]);
            $this->downloads->remove($download);

            $parentId = $download->getParentId();
            $remaining = count($this->downloads->getByParent($parentId));

            if ($remaining === 0) {
                $this->logger->info('Removing parent from put.io.', ['parent_id' => $parentId]);
                $this->putio->files->delete($parentId);
            } else {
                $message = $remaining === 1 ? '{count} child still remains for this parent.' : '{count} children still remain for this parent.';
                $this->logger->info($message, ['count' => $remaining, 'parent_id' => $parentId]);
            }
        } else{
            $this->logger->error('Unable to find download with filename "{filename}".', ['filename' => $filename]);
        }
    }
}
