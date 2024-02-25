<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\Station;

use Persium\Station\Domain\Entities\Station\StationSensorData;
use Persium\Station\Domain\Entities\Station\StationSensor;
use Tests\TestCase;
use DateTimeImmutable;

class StationSensorDataTest extends TestCase
{
    public function testConstructor()
    {
        $station_sensor = $this->createMock(StationSensor::class);
        $timestamp = new DateTimeImmutable();
        $station_data_detail = new StationSensorData(
            sensor: $station_sensor,
            timestamp: $timestamp,
            ugm3: 1.0,
            value: 3.0,
            ppb: 2.0,
        );

        $this->assertEquals($station_sensor, $station_data_detail->getSensor());
        $this->assertEquals($timestamp, $station_data_detail->getTimestamp());
        $this->assertEquals(1.0, $station_data_detail->getUGM3());
        $this->assertEquals(3.0, $station_data_detail->getValue());
        $this->assertEquals(2.0, $station_data_detail->getPPB());
    }

    public function testConstructorWithNull()
    {
        $station_sensor = $this->createMock(StationSensor::class);
        $timestamp = new DateTimeImmutable();
        $station_data_detail = new StationSensorData(
            sensor: $station_sensor,
            timestamp: $timestamp,
            ugm3: null,
            value: null,
            ppb: null,
        );

        $this->assertEquals($station_sensor, $station_data_detail->getSensor());
        $this->assertEquals($timestamp, $station_data_detail->getTimestamp());
        $this->assertEquals(null, $station_data_detail->getUGM3());
        $this->assertEquals(null, $station_data_detail->getValue());
        $this->assertEquals(null, $station_data_detail->getPPB());
    }

    public function testSetters()
    {
        $station_sensor = $this->createMock(StationSensor::class);
        $timestamp = new DateTimeImmutable();
        $station_data_detail = new StationSensorData(
            sensor: $station_sensor,
            timestamp: $timestamp,
            ugm3: 1.0,
            value: 2.0,
            ppb: 3.0,
        );

        $station_data_detail->setUGM3(2.0);
        $station_data_detail->setValue(4.0);
        $station_data_detail->setPPB(6.0);
        $this->assertEquals(2.0, $station_data_detail->getUGM3());
        $this->assertEquals(4.0, $station_data_detail->getValue());
        $this->assertEquals(6.0, $station_data_detail->getPPB());
    }
}
