<?php

namespace SlaveMarket\Lease;

use DateTime;

/**
 * Репозиторий договоров аренды
 *
 * @package SlaveMarket\Lease
 */
interface LeaseContractsRepository
{
    /**
     * Возвращает список договоров аренды для раба, в которых заняты часы из указанного периода
     *
     * @param int $slaveId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return LeaseContract[]
     */
    public function getForSlave(int $slaveId, DateTime $dateFrom, DateTime $dateTo) : array;
}