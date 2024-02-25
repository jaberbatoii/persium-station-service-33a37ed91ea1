<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\SensorType;

use Persium\Station\Domain\Entities\Station\StationSensor;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Str;

class Vendor
{
    private int $id;
    private string $uuid;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private ?DateTimeInterface $deleted_at = null;

    private ?ArrayCollection $sensors = null;

    public function __construct(
        private string $name,
        private string $display_name,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): void
    {
        $this->display_name = $display_name;
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

    public function getSensors(): ?ArrayCollection
    {
        return $this->sensors;
    }

    public function addSensor(StationSensor $sensor): void
    {
        $this->sensors->add($sensor);
        $sensor->setVendor($this);
    }
}
