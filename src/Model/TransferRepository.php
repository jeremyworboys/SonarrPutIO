<?php

namespace JeremyWorboys\SonarrPutIO\Model;

interface TransferRepository
{
    /**
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer[]
     */
    public function all(): array;

    /**
     * @param int $id
     * @return \JeremyWorboys\SonarrPutIO\Model\Transfer|null
     */
    public function get(int $id): ?Transfer;

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function add(Transfer $transfer);

    /**
     * @param \JeremyWorboys\SonarrPutIO\Model\Transfer $transfer
     */
    public function remove(Transfer $transfer);
}
