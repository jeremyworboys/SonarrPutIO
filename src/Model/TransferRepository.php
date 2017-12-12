<?php

namespace JeremyWorboys\SonarrPutIO\Model;

class TransferRepository
{
    /** @var string */
    private $filename;

    /** @var \JeremyWorboys\SonarrPutIO\Model\Transfer[] */
    private $transfers;

    /**
     * TransferRepository constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->loadData();
    }

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
        $this->transfers[] = $transfer;
        $this->flushData();
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function remove(Transfer $transfer)
    {
        $index = array_search($transfer, $this->transfers, true);

        if ($index !== false) {
            unset($this->transfers[$index]);
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

        $this->transfers = [];
        foreach ($lines as $line) {
            [$id, $name] = explode("\t", $line);
            $this->transfers[] = new Transfer($id, $name);
        }
    }

    /**
     */
    private function flushData(): void
    {
        $lines = [];
        foreach ($this->transfers as $transfer) {
            $lines[] = $transfer->getId() . "\t" . $transfer->getName();
        }

        $contents = implode(PHP_EOL, $lines) . PHP_EOL;
        file_put_contents($this->filename, $contents);
    }
}
