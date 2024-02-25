<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\Station;

use DateTimeInterface;

class StationSensorData
{
    public function __construct(
        private StationSensor              $sensor,
        private readonly DateTimeInterface $timestamp,
        private ?float                     $ugm3,
        private ?float                     $value,
        private ?float                     $ppb,
    ) {
    }

    public function getSensor(): StationSensor
    {
        return $this->sensor;
    }

    public function setSensor(StationSensor $station_sensor): void
    {
        $this->sensor = $station_sensor;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getUGM3(): ?float
    {
        return $this->ugm3;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getPPB(): ?float
    {
        return $this->ppb;
    }

    public function setUGM3(?float $ugm3): void
    {
        $this->ugm3 = $ugm3;
    }

    public function setValue(?float $value): void
    {
        $this->value = $value;
    }

    public function setPPB(?float $ppb): void
    {
        $this->ppb = $ppb;
    }
}
