<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;

class StationVO
{
    public function __construct(
        private readonly string $name,
        private readonly string $address,
        private readonly string $source,
        private readonly string $source_id,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly float $altitude,
        private readonly ?string $photo_url,
        private readonly ?DateTimeInterface $installed_at,
        private readonly int $status = 2,
    ) {
    }

    public function getInstalledAt(): ?DateTimeInterface
    {
        return $this->installed_at;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getSourceID(): string
    {
        return $this->source_id;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getAltitude(): float
    {
        return $this->altitude;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photo_url;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
