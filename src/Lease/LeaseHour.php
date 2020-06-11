<?php

namespace SlaveMarket\Lease;

use DateTime;

/**
 * Арендованный час
 *
 * @package SlaveMarket\Lease
 */
class LeaseHour
{
    /**
     * Время начала часа
     *
     * @var DateTime
     */
    protected $dateTime;

    /**
     * LeaseHour constructor.
     *
     * @param DateTime $dateTime
     */
    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Возвращает строку, представляющую час
     *
     * @return string
     */
    public function getDateString(): string
    {
        return $this->dateTime->format('Y-m-d H');
    }

    /**
     * Возвращает объект даты
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * Возвращает день аренды
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->dateTime->format('Y-m-d');
    }

    /**
     * Возвращает час аренды
     *
     * @return string
     */
    public function getHour(): string
    {
        return $this->dateTime->format('H');
    }
}
