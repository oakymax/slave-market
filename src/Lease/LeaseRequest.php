<?php

namespace SlaveMarket\Lease;

use DateTime;

/**
 * Запрос на аренду раба
 *
 * @package SlaveMarket\Lease
 */
class LeaseRequest
{
    /** @var int id хозяина */
    protected $masterId;

    /** @var int id раба */
    protected $slaveId;

    /** @var DateTime время начала работ */
    protected $timeFrom;

    /** @var DateTime время окончания работ */
    protected $timeTo;

    public function __construct($masterId, $slaveId, DateTime $timeFrom, DateTime $timeTo)
    {
        $this->masterId = $masterId;
        $this->slaveId = $slaveId;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
    }
}