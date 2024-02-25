<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\Station;

use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\Entities\SensorType\Vendor;
use Persium\Station\Domain\Entities\Station\StationSensor;
use Persium\Station\Domain\Entities\Station\Station;
use Tests\TestCase;

class StationSensorTest extends TestCase
{
    //setup
    public function setUp(): void
    {
        parent::setUp();
    }
    public function testConstruct()
    {
        $station = $this->createMock(Station::class);
        $type = $this->createMock(SensorType::class);
        $vendor = $this->createMock(Vendor::class);

        $sensor = new StationSensor(
            station: $station,
            type: $type,
            vendor: $vendor,
            name: 'name',
            unit: 'unit',
            location: 1,
            status: 1,
            sr1: 1.0,
            sr2: 2.0,
            ar1: 3.0,
            ar2: 4.0,
            sensitivity: 5.0,
            second_sensitivity: 6.0,
            vp_code: 7.0,
            aux_base: 8.0,
            sensor_base: 9.0,
            installed_at: new \DateTimeImmutable(),
        );
        $this->assertEquals($station, $sensor->getStation());
        $this->assertEquals($type, $sensor->getType());
        $this->assertEquals($vendor, $sensor->getVendor());
        $this->assertEquals('name', $sensor->getName());
        $this->assertEquals('unit', $sensor->getUnit());
        $this->assertEquals(1, $sensor->getOffset());
        $this->assertEquals(1, $sensor->getStatus());
        $this->assertEquals(1.0, $sensor->getSr1());
        $this->assertEquals(2.0, $sensor->getSr2());
        $this->assertEquals(3.0, $sensor->getAr1());
        $this->assertEquals(4.0, $sensor->getAr2());
        $this->assertEquals(5.0, $sensor->getSensitivity());
        $this->assertEquals(6.0, $sensor->getSecondSensitivity());
        $this->assertEquals(7.0, $sensor->getVPCode());
        $this->assertEquals(8.0, $sensor->getAuxBase());
        $this->assertEquals(9.0, $sensor->getSensorBase());
        $this->assertInstanceOf(StationSensor::class, $sensor);
    }

    public function testSetters()
    {
        $station = $this->createMock(Station::class);
        $type = $this->createMock(SensorType::class);
        $vendor = $this->createMock(Vendor::class);

        $sensor = new StationSensor(
            station: $station,
            type: $type,
            vendor: $vendor,
            name: 'name',
            unit: 'unit',
            location: 1,
            status: 1,
            sr1: 1.0,
            sr2: 2.0,
            ar1: 3.0,
            ar2: 4.0,
            sensitivity: 5.0,
            second_sensitivity: 6.0,
            vp_code: 7.0,
            aux_base: 8.0,
            sensor_base: 9.0,
            installed_at: new \DateTimeImmutable(),
        );
        $sensor->setOffset(2);
        $sensor->setStatus(2);
        $sensor->setSr1(3.0);
        $sensor->setSr2(4.0);
        $sensor->setAr1(5.0);
        $sensor->setAr2(6.0);
        $sensor->setSensitivity(7.0);
        $sensor->setSecondSensitivity(8.0);
        $sensor->setVPCode(9.0);
        $sensor->setAuxBase(10.0);
        $sensor->setSensorBase(11.0);
        $sensor->setName('name2');
        $sensor->setUnit('unit2');
        $this->assertEquals('name2', $sensor->getName());
        $this->assertEquals('unit2', $sensor->getUnit());
        $this->assertEquals(2, $sensor->getOffset());
        $this->assertEquals(2, $sensor->getStatus());
        $this->assertEquals(3.0, $sensor->getSr1());
        $this->assertEquals(4.0, $sensor->getSr2());
        $this->assertEquals(5.0, $sensor->getAr1());
        $this->assertEquals(6.0, $sensor->getAr2());
        $this->assertEquals(7.0, $sensor->getSensitivity());
        $this->assertEquals(8.0, $sensor->getSecondSensitivity());
        $this->assertEquals(9.0, $sensor->getVPCode());
        $this->assertEquals(10.0, $sensor->getAuxBase());
        $this->assertEquals(11.0, $sensor->getSensorBase());
    }
}
