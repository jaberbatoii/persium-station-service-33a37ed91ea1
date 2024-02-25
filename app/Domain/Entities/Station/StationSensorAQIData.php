<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\Station;

use DateTimeInterface;
use Illuminate\Support\Str;
use DateTimeImmutable;

class StationSensorAQIData
{

    public function __construct(
        private StationSensor              $sensor,
        private int                        $value,
        private int                        $type,
        private readonly DateTimeInterface $timestamp,
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

    public function getValue(): int
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }
}
