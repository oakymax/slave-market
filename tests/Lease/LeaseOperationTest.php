<?php

namespace SlaveMarket\Lease;

use DateTime;
use Decimal\Decimal;
use PHPUnit\Framework\TestCase;
use SlaveMarket\Master;
use SlaveMarket\MastersRepository;
use SlaveMarket\Slave;
use SlaveMarket\SlavesRepository;

/**
 * Тесты операции аренды раба
 *
 * @package SlaveMarket\Lease
 */
class LeaseOperationTest extends TestCase
{
    /**
     * Stub репозитория хозяев
     *
     * @param Master[] ...$masters
     * @return MastersRepository
     */
    private function makeFakeMasterRepository(...$masters): MastersRepository
    {
        $mastersRepository = $this->prophesize(MastersRepository::class);
        foreach ($masters as $master) {
            $mastersRepository->getById($master->getId())->willReturn($master);
        }

        return $mastersRepository->reveal();
    }

    /**
     * Stub репозитория рабов
     *
     * @param Slave[] ...$slaves
     * @return SlavesRepository
     */
    private function makeFakeSlaveRepository(...$slaves): SlavesRepository
    {
        $slavesRepository = $this->prophesize(SlavesRepository::class);
        foreach ($slaves as $slave) {
            $slavesRepository->getById($slave->getId())->willReturn($slave);
        }

        return $slavesRepository->reveal();
    }

    /**
     * Создание DateTime из строки с датой и временем
     * (синтаксический сахар)
     *
     * @param string $timeString время, в формате `Y-m-d H:i:s`
     * @return DateTime
     */
    private function pickTime($timeString) : DateTime
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $timeString);
    }

    /**
     * Создание DateTime из строки с датой и часом (0-23)
     * (синтаксический сахар)
     *
     * @param string $timeString время, в формате `Y-m-d H`
     * @return DateTime
     */
    private function pickHour($timeString) : DateTime
    {
        return DateTime::createFromFormat('Y-m-d H', $timeString);
    }

    /**
     * Создание DateTime из строки с датой
     * (синтаксический сахар)
     *
     * @param string $timeString время, в формате `Y-m-d`
     * @return DateTime
     */
    private function pickDay($timeString) : DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $timeString);
    }

    /**
     * Если раб занят, то арендовать его не получится
     */
    public function test_periodIsBusy_failedWithOverlapInfo()
    {
        // -- Arrange
        {
            // Хозяева
            $master1    = new Master(1, 'Господин Боб');
            $master2    = new Master(2, 'сэр Вонючка');
            $masterRepo = $this->makeFakeMasterRepository($master1, $master2);

            // Раб
            $slave1    = new Slave(1, 'Уродливый Фред', new Decimal('20', 2));
            $slaveRepo = $this->makeFakeSlaveRepository($slave1);

            // Договор аренды. 1й хозяин арендовал раба
            $leaseContract1 = new LeaseContract($master1, $slave1, new Decimal('80', 2), [
                new LeaseHour($this->pickHour('2017-01-01 00')),
                new LeaseHour($this->pickHour('2017-01-01 01')),
                new LeaseHour($this->pickHour('2017-01-01 02')),
                new LeaseHour($this->pickHour('2017-01-01 03')),
            ]);

            // Stub репозитория договоров
            $contractsRepo = $this->prophesize(LeaseContractsRepository::class);
            $contractsRepo
                ->getForSlave($slave1->getId(), $this->pickDay('2017-01-01'), $this->pickDay('2017-01-01'))
                ->willReturn([$leaseContract1]);

            // Запрос на новую аренду. 2й хозяин выбрал занятое время
            $leaseRequest = new LeaseRequest(
                $master2->getId(),
                $slave1->getId(),
                $this->pickTime('2017-01-01 01:30:00'),
                $this->pickTime('2017-01-01 02:01:00')
            );

            // Операция аренды
            $leaseOperation = new LeaseOperation($contractsRepo->reveal(), $masterRepo, $slaveRepo);
        }

        // -- Act
        $response = $leaseOperation->run($leaseRequest);

        // -- Assert
        $expectedErrors = ['Ошибка. Раб #1 "Уродливый Фред" занят. Занятые часы: "2017-01-01 01", "2017-01-01 02"'];

        $this->assertArraySubset($expectedErrors, $response->getErrors());
        $this->assertNull($response->getLeaseContract());
    }

    /**
     * Если раб бездельничает, то его легко можно арендовать
     */
    public function test_idleSlave_successfullyLeased ()
    {
        // -- Arrange
        {
            // Хозяева
            $master1    = new Master(1, 'Господин Боб');
            $masterRepo = $this->makeFakeMasterRepository($master1);

            // Раб
            $slave1    = new Slave(1, 'Уродливый Фред', new Decimal('20', 2));
            $slaveRepo = $this->makeFakeSlaveRepository($slave1);

            $contractsRepo = $this->prophesize(LeaseContractsRepository::class);
            $contractsRepo
                ->getForSlave($slave1->getId(), $this->pickDay('2017-01-01'), $this->pickDay('2017-01-01'))
                ->willReturn([]);

            // Запрос на новую аренду
            $leaseRequest = new LeaseRequest(
                $master1->getId(),
                $slave1->getId(),
                $this->pickTime('2017-01-01 01:30:00'),
                $this->pickTime('2017-01-01 02:01:00')
            );

            // Операция аренды
            $leaseOperation = new LeaseOperation($contractsRepo->reveal(), $masterRepo, $slaveRepo);
        }

        // -- Act
        $response = $leaseOperation->run($leaseRequest);

        // -- Assert
        $this->assertEmpty($response->getErrors());
        $this->assertInstanceOf(LeaseContract::class, $response->getLeaseContract());
        $this->assertEquals(40, $response->getLeaseContract()->price);
    }
}