<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\SensorType;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Str;
use DateTimeImmutable;

class CrossSensitivity
{
    private int $id;
    private string $uuid;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private ?DateTimeInterface $deleted_at = null;

    public function __construct(
        private SensorType $sensor_type,
        private string $name,
        private float $adjustment,
        private float $concentration,
    ) {
        $this->uuid = Str::uuid()->toString();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = $this->created_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function getSensorType(): SensorType
    {
        return $this->sensor_type;
    }

    public function setSensorType(SensorType $sensor_type): void
    {
        $this->sensor_type = $sensor_type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAdjustment(): float
    {
        return $this->adjustment;
    }

    public function setAdjustment(float $adjustment): void
    {
        $this->adjustment = $adjustment;
    }

    public function getConcentration(): float
    {
        return $this->concentration;
    }

    public function setConcentration(float $concentration): void
    {
        $this->concentration = $concentration;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(): void
    {
        $this->deleted_at = new DateTime();
    }
}
