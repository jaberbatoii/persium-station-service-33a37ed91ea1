<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\Station;

use DateTimeImmutable;
use DateTimeInterface;

class StationAQIData
{

    const TYPE_DAQI = 1;
    const TYPE_CAQI = 2;
    const TYPE_USAQI = 3;

    public function __construct(
        private Station $station,
        private int     $value,
        private int     $type,
        private string     $factor,
        private DateTimeInterface $timestamp
    ) {
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function setStation(Station $station): void
    {
        $this->station = $station;
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

    public function getFactor(): string
    {
        return $this->factor;
    }

    public function setFactor(string $factor): void
    {
        $this->factor = $factor;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
