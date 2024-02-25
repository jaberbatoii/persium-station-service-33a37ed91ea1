<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\ValueObjects;

use DateTimeInterface;

class StationSensorDataVO
{
    public function __construct(
        private readonly DateTimeInterface $timestamp,
        private readonly string $name,
        private readonly ?float $value,
        private readonly ?float $ugm3,
        private readonly ?float $ppb,
        private readonly string $unit,
        private readonly ?int $daqi,
        private readonly ?int $caqi,
        private readonly ?int $usaqi,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getCAQI(): ?int
    {
        return $this->caqi;
    }

    public function getUSAQI(): ?int
    {
        return $this->usaqi;
    }

    public function getDAQI(): ?int
    {
        return $this->daqi;
    }

    public function getUGM3(): ?float
    {
        return $this->ugm3;
    }

    public function getPPB(): ?float
    {
        return $this->ppb;
    }
}
