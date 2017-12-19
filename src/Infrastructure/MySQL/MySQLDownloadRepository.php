<?php

namespace JeremyWorboys\SonarrPutIO\Infrastructure\MySQL;

use JeremyWorboys\SonarrPutIO\Model\Download;
use JeremyWorboys\SonarrPutIO\Model\DownloadRepository;

class MySQLDownloadRepository implements DownloadRepository
{
    /** @var \PDO */
    private $conn;

    /**
     * MySQLTransferRepository constructor.
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function all(): array
    {
        $stmt = $this->conn->query('SELECT id, parent_id, filename FROM downloads');
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->buildResultArray($rows);
    }

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function get(int $id): ?Download
    {
        $stmt = $this->conn->prepare('SELECT id, parent_id, filename FROM downloads WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->buildResult($data);
    }

    /**
     * @param int $parentId
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    public function getByParent(int $parentId): array
    {
        $stmt = $this->conn->prepare('SELECT id, parent_id, filename FROM downloads WHERE parent_id = :parent');
        $stmt->bindValue('parent', $parentId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->buildResultArray($rows);
    }

    /**
     * @param string $filename
     * @return \JeremyWorboys\SonarrPutIO\Model\Download|null
     */
    public function getByFilename(string $filename): ?Download
    {
        $stmt = $this->conn->prepare('SELECT id, parent_id, filename FROM downloads WHERE filename = :filename');
        $stmt->bindValue(':filename', $filename, \PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->buildResult($data);
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function add(Download $download)
    {
        $stmt = $this->conn->prepare('INSERT INTO downloads (id, parent_id, filename) VALUES (:id, :parent, :filename)');
        $stmt->bindValue(':id', $download->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(':parent', $download->getParentId(), \PDO::PARAM_INT);
        $stmt->bindValue(':filename', $download->getFilename(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Download $download
     */
    public function remove(Download $download)
    {
        $stmt = $this->conn->prepare('DELETE FROM downloads WHERE id = :id');
        $stmt->bindValue(':id', $download->getId(), \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @param array $data
     * @return \JeremyWorboys\SonarrPutIO\Model\Download
     */
    private function buildResult(array $data): Download
    {
        return new Download((int) $data['id'], (int) $data['parent_id'], $data['filename']);
    }

    /**
     * @param array $rows
     * @return \JeremyWorboys\SonarrPutIO\Model\Download[]
     */
    private function buildResultArray(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->buildResult($row);
        }

        return $result;
    }
}
