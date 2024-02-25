<?php

declare(strict_types = 1);

namespace Tests\Unit\Entities\Station;

use Persium\Station\Domain\Entities\Station\Station;
use Persium\Station\Domain\Entities\Station\StationSensorData;
use Persium\Station\Domain\Entities\Station\StationAQIData;
use Tests\TestCase;
use DateTimeImmutable;

class StationAQIDataTest extends TestCase
{
    public function testConstructor(): void
    {
        $station = $this->createMock(Station::class);
        $station_aqi_data = new StationAQIData(
            station: $station,
            value: 1,
            type: 1,
            factor: 'pm25',
            timestamp: new DateTimeImmutable(),
        );

        $this->assertEquals($station, $station_aqi_data->getStation());
        $this->assertEquals(1, $station_aqi_data->getValue());
        $this->assertEquals(1, $station_aqi_data->getType());
        $this->assertEquals('pm25', $station_aqi_data->getFactor());
    }

    public function testSetters()
    {
        $station = $this->createMock(Station::class);
        $station_aqi_data = new StationAQIData(
            station: $station,
            value: 1,
            type: 1,
            factor: 'pm25',
            timestamp: new DateTimeImmutable(),
        );

        $station_aqi_data->setValue(2);
        $station_aqi_data->setType(2);
        $station_aqi_data->setFactor('pm10');

        $this->assertEquals(2, $station_aqi_data->getValue());
        $this->assertEquals(2, $station_aqi_data->getType());
        $this->assertEquals('pm10', $station_aqi_data->getFactor());
    }
}
