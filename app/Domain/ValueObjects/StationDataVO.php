<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\ValueObjects;

use Illuminate\Support\Collection;
use Persium\Station\Domain\Entities\Station\Station;
use DateTimeInterface;

class StationDataVO
{
    public function __construct(
        private readonly Station $station,
        private readonly DateTimeInterface $timestamp,
        private readonly array $sensor_data_vos,
        private readonly ?int $caqi,
        private readonly string $caqi_factor,
        private readonly ?int $usaqi,
        private readonly string $usaqi_factor,
        private readonly ?int $daqi,
        private readonly string $daqi_factor,
    ) {
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getSensorDataVOs(): array
    {
        return $this->sensor_data_vos;
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

    public function getCAQIFactor(): string
    {
        return $this->caqi_factor;
    }

    public function getUSAQIFactor(): string
    {
        return $this->usaqi_factor;
    }

    public function getDAQIFactor(): string
    {
        return $this->daqi_factor;
    }
}
