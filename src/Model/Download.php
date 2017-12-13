<?php

namespace JeremyWorboys\SonarrPutIO\Model;

class Download
{
    /** @var int */
    private $id;

    /** @var int */
    private $parentId;

    /** @var string */
    private $filename;

    /**
     * Download constructor.
     *
     * @param int    $id
     * @param int    $parentId
     * @param string $filename
     */
    public function __construct(int $id, int $parentId, string $filename)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}
