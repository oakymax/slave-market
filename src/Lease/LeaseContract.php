<?php

namespace SlaveMarket\Lease;

use Decimal\Decimal;
use SlaveMarket\Master;
use SlaveMarket\Slave;

/**
 * Договор аренды
 *
 * @package SlaveMarket\Lease
 */
class LeaseContract
{
    /** @var Master Хозяин */
    public $master;

    /** @var Slave Раб */
    public $slave;

    /** @var Decimal Стоимость */
    public $price = 0;

    /** @var LeaseHour[] Список арендованных часов */
    public $leasedHours = [];

    public function __construct(Master $master, Slave $slave, Decimal $price, array $leasedHours)
    {
        $this->master      = $master;
        $this->slave       = $slave;
        $this->price       = $price;
        $this->leasedHours = $leasedHours;
    }
}
