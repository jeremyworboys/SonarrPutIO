<?php

namespace JeremyWorboys\SonarrPutIO;

use JeremyWorboys\SonarrPutIO\Model\Download;
use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;
use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;
use JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader;
use JeremyWorboys\SonarrPutIO\Service\PutIO\LinkFinder;
use Psr\Log\LoggerInterface;
use PutIO\API;
use PutIO\Exceptions\RemoteConnectionException;

class DownloadHandler
{
    private const MIN_FILE_SIZE = 10000000; // 10 MB

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PutIO\API */
    private $putio;

    /** @var \JeremyWorboys\SonarrPutIO\Service\PutIO\LinkFinder */
    private $finder;

    /** @var \JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader */
    private $macPsd;

    /** @var \JeremyWorboys\SonarrPutIO\Model\DownloadRepository */
    private $downloads;

    /** @var \JeremyWorboys\SonarrPutIO\Model\TransferRepository */
    private $transfers;

    /** @var string */
    private $root;

    /**
     * Downloader constructor.
     *
     * @param \Psr\Log\LoggerInterface                                 $logger
     * @param \PutIO\API                                               $putio
     * @param \JeremyWorboys\SonarrPutIO\Service\ProgressiveDownloader $macPsd
     * @param \JeremyWorboys\SonarrPutIO\Model\DownloadRepository      $downloads
     * @param \JeremyWorboys\SonarrPutIO\Model\TransferRepository      $transfers
     * @param string                                                   $root
     */
    public function __construct(LoggerInterface $logger, API $putio, ProgressiveDownloader $macPsd, DownloadRepository $downloads, TransferRepository $transfers, string $root)
    {
        $this->logger = $logger;
        $this->putio = $putio;
        $this->finder = new LinkFinder($putio);
        $this->macPsd = $macPsd;
        $this->downloads = $downloads;
        $this->transfers = $transfers;
        $this->root = $root;
    }

    /**
     */
    public function run()
    {
        $this->logger->info('Checking for completed downloads.');
        $iter = new \FilesystemIterator($this->root, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_PATHNAME);
        foreach ($iter as $filename) {
            if (basename($filename) === '.DS_Store') {
                continue;
            }

            $this->logger->info('Checking download with filename "{filename}".', ['filename' => $filename]);

            if (substr($filename, -11) === '.psdownload') {
                $this->logger->info('Skipping incomplete download with filename "{filename}".', ['filename' => $filename]);
                continue;
            }

            $this->markComplete($filename);
        }

        $this->logger->info('Checking for completed transfers.');
        foreach ($this->transfers->all() as $transfer) {
            $this->handleTransfer($transfer);
        }

        $this->logger->info('Cleaning put.io transfer list.');
        $this->putio->transfers->clean();
    }

    /**
     * @param int $transferId
     */
    public function runOnce(int $transferId)
    {
        $this->logger->info('Running downloader for single transfer.');

        $transfer = $this->transfers->get($transferId);

        if ($transfer) {
            $this->handleTransfer($transfer);
        } else {
            $this->logger->error('Could not find transfer with id "{transfer_id}".', ['transfer_id' => $transferId]);
        }

        $this->logger->info('Cleaning put.io transfer list.');
        $this->putio->transfers->clean();
    }

    /**
     * @param string $filename
     */
    public function markComplete(string $filename)
    {
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
        } else {
            $this->logger->error('Unable to find download with filename "{filename}".', ['filename' => $filename]);
        }
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    private function handleTransfer(Transfer $transfer)
    {
        $info = $this->getTransferInfo($transfer);

        if ($info === null) {
            $this->logger->error('Could not get info about transfer with id "{transfer_id}".', ['transfer_id' => $transfer->getId()]);
            return;
        }

        $this->logger->info('Found transfer with id "{transfer_id}".', [
            'transfer_id' => $transfer->getId(),
            'info'        => $info,
        ]);
        $status = $info['status'];

        if ($status !== 'SEEDING' && $status !== 'COMPLETED') {
            $this->logger->info('Skipping incomplete transfer.');
            return;
        }

        if ($status === 'SEEDING') {
            $this->logger->info('Cancelling seeding transfer.');
            $this->putio->transfers->cancel($transfer->getId());
        }

        if ($status === 'SEEDING' || $status === 'COMPLETED') {
            $this->logger->info('Downloading files for completed transfer.');
            $this->download($info['file_id']);
            $this->transfers->remove($transfer);
        }
    }

    /**
     * @param int $parentId
     */
    private function download(int $parentId)
    {
        $links = $this->finder->getDownloadLinks($parentId, true);
        $this->logger->info('Found download links for completed transfer.', ['links' => $links]);

        $this->macPsd->launchApp();
        foreach ($links as $link) {
            $this->logger->info('Found download links for completed transfer.', ['links' => $links]);
            if (preg_match('~/files/(\d+)/download~', $link, $matches)) {
                $file = $this->putio->files->info($matches[1]);

                if ($file === null) {
                    $this->logger->error('Could not get info about file id "{file_id}".', ['file_id' => $file['id']]);
                    return;
                }

                if ($file['size'] < self::MIN_FILE_SIZE) {
                    $this->logger->info('Skipping "{file_name}" due to filesize.', [
                        'file_name' => $file['name'],
                        'file_size' => $file['size'],
                    ]);
                    continue;
                }

                $this->macPsd->addTask($link);
                $this->appendDownloadList($parentId, $file);
            } else {
                $this->logger->error('Could not extract file id from link "{link}".', ['link' => $link]);
            }
        }
    }

    /**
     * @param int   $parentId
     * @param array $file
     */
    private function appendDownloadList(int $parentId, array $file)
    {
        $filename = $this->root . '/' . $file['name'];
        $download = new Download($file['id'], $parentId, $filename);
        $this->downloads->add($download);
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     * @return array
     */
    private function getTransferInfo(Transfer $transfer): ?array
    {
        try {
            $info = $this->putio->transfers->info($transfer->getId());
        } catch (RemoteConnectionException $e) {
            if ($e->getCode() === 404) {
                $this->transfers->remove($transfer);
            }
            return null;
        }

        return $info['transfer'];
    }
}
