<?php

namespace SlaveMarket;

use Decimal\Decimal;

/**
 * Раб (Бедняга :-()
 *
 * @package SlaveMarket
 */
class Slave
{
    /** @var int id раба */
    protected $id;

    /** @var string имя раба */
    protected $name;

    /** @var Decimal Стоимость раба за час работы */
    protected $pricePerHour;

    /**
     * Slave constructor.
     *
     * @param int $id
     * @param string $name
     * @param float $pricePerHour
     */
    public function __construct(int $id, string $name, Decimal $pricePerHour)
    {
        $this->id           = $id;
        $this->name         = $name;
        $this->pricePerHour = $pricePerHour;
    }

    /**
     * Возвращает id раба
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает имя раба
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает стоимость раба за час
     *
     * @return Decimal
     */
    public function getPricePerHour(): Decimal
    {
        return $this->pricePerHour;
    }
}