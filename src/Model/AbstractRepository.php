<?php

namespace JeremyWorboys\SonarrPutIO\Model;

abstract class AbstractRepository
{
    /** @var string */
    protected $filename;

    /**
     * AbstractRepository constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->loadData();
    }

    /**
     * @param array $lines
     */
    abstract protected function readLines(array $lines): void;

    /**
     * @return array
     */
    abstract protected function writeLines(): array;

    /**
     */
    protected function loadData(): void
    {
        $contents = file_get_contents($this->filename);
        $lines = explode(PHP_EOL, $contents);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        $this->readLines($lines);
    }

    /**
     */
    protected function flushData(): void
    {
        $lines = $this->writeLines();
        $contents = implode(PHP_EOL, $lines) . PHP_EOL;

        file_put_contents($this->filename, $contents);
    }

    /**
     * @param \Closure $callback
     */
    protected function withWriteLock(\Closure $callback): void
    {
        $lock = $this->filename . '.lock';
        $fh = fopen($lock, 'c');
        flock($fh, LOCK_EX);

        $callback();

        unlink($lock);
        flock($fh, LOCK_UN);
        fclose($fh);
        $fh = null;
    }
}
