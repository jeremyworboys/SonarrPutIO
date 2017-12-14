<?php

namespace JeremyWorboys\SonarrPutIO\Infrastructure\FlatFile;

use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;

class FlatFileTransferRepository extends FlatFileRepository implements TransferRepository
{
    /** @var \JeremyWorboys\SonarrPutIO\Model\Transfer[] */
    private $transfers;

    /**
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer[]
     */
    public function all(): array
    {
        return $this->transfers;
    }

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer|null
     */
    public function get(int $id): ?Transfer
    {
        foreach ($this->transfers as $transfer) {
            if ($transfer->getId() === $id) {
                return $transfer;
            }
        }

        return null;
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function add(Transfer $transfer)
    {
        //$this->withWriteLock(function () use ($transfer) {
        //    $this->loadData();
        $this->transfers[] = $transfer;
        $this->flushData();
        //});
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function remove(Transfer $transfer)
    {
        //$this->withWriteLock(function () use ($transfer) {
        //    $this->loadData();
        $index = array_search($transfer, $this->transfers, true);

        if ($index !== false) {
            unset($this->transfers[$index]);
            $this->flushData();
        }
        //});
    }

    /**
     * @param array $lines
     */
    protected function readLines(array $lines): void
    {
        $this->transfers = [];
        foreach ($lines as $line) {
            [$id, $name] = explode("\t", $line);
            $this->transfers[] = new Transfer($id, $name);
        }
    }

    /**
     * @return array
     */
    protected function writeLines(): array
    {
        $lines = [];
        foreach ($this->transfers as $transfer) {
            $lines[] = $transfer->getId() . "\t" . $transfer->getName();
        }

        return $lines;
    }
}
