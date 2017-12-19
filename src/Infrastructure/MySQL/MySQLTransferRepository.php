<?php

namespace JeremyWorboys\SonarrPutIO\Infrastructure\MySQL;

use JeremyWorboys\SonarrPutIO\Model\Transfer;
use JeremyWorboys\SonarrPutIO\Model\TransferRepository;

class MySQLTransferRepository implements TransferRepository
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
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer[]
     */
    public function all(): array
    {
        $stmt = $this->conn->query('SELECT id, name FROM transfers');
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->buildResultArray($rows);
    }

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer|null
     */
    public function get(int $id): ?Transfer
    {
        $stmt = $this->conn->prepare('SELECT id, name FROM transfers WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->buildResult($data);
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function add(Transfer $transfer)
    {
        $stmt = $this->conn->prepare('INSERT INTO transfers (id, name) VALUES (:id, :name)');
        $stmt->bindValue(':id', $transfer->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(':name', $transfer->getName(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function remove(Transfer $transfer)
    {
        $stmt = $this->conn->prepare('DELETE FROM transfers WHERE id = :id');
        $stmt->bindValue(':id', $transfer->getId(), \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @param array $data
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer
     */
    private function buildResult(array $data): Transfer
    {
        return new Transfer((int) $data['id'], $data['name']);
    }

    /**
     * @param array $rows
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer[]
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
