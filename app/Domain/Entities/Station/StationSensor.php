<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\Station;

use Doctrine\Common\Collections\Collection;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\Entities\SensorType\Vendor;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Str;

class StationSensor
{
    private int $id;
    private string $uuid;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private ?DateTimeInterface $deleted_at = null;

    private Collection $data;
    private Collection $aqi_data;

    public function __construct(
        private Station $station,
        private SensorType $type,
        private ?Vendor $vendor,
        private string $name,
        private ?string $unit,
        private ?int $location,
        private int $status,
        private ?float $sr1,
        private ?float $sr2,
        private ?float $ar1,
        private ?float $ar2,
        private ?float $sensitivity,
        private ?float $second_sensitivity,
        private ?float $vp_code,
        private ?float $aux_base,
        private ?float $sensor_base,
        private ?DateTimeInterface $installed_at,
    ) {
        $this->uuid = Str::uuid()->toString();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
        $this->data = new ArrayCollection();
        $this->aqi_data = new ArrayCollection();
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function getType(): SensorType
    {
        return $this->type;
    }

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getOffset(): ?int
    {
        return $this->location;
    }

    public function setOffset(?int $location): void
    {
        $this->location = $location;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getSr1(): ?float
    {
        return $this->sr1;
    }

    public function setSr1(float $sr1): void
    {
        $this->sr1 = $sr1;
    }

    public function getSr2(): ?float
    {
        return $this->sr2;
    }

    public function setSr2(float $sr2): void
    {
        $this->sr2 = $sr2;
    }

    public function getAr1(): ?float
    {
        return $this->ar1;
    }

    public function setAr1(float $ar1): void
    {
        $this->ar1 = $ar1;
    }

    public function getAr2(): ?float
    {
        return $this->ar2;
    }

    public function setAr2(float $ar2): void
    {
        $this->ar2 = $ar2;
    }

    public function getSensitivity(): ?float
    {
        return $this->sensitivity;
    }

    public function setSensitivity(float $sensitivity): void
    {
        $this->sensitivity = $sensitivity;
    }

    public function getSecondSensitivity(): ?float
    {
        return $this->second_sensitivity;
    }

    public function setSecondSensitivity(float $second_sensitivity): void
    {
        $this->second_sensitivity = $second_sensitivity;
    }

    public function getVPCode(): ?float
    {
        return $this->vp_code;
    }

    public function setVPCode(float $vp_code): void
    {
        $this->vp_code = $vp_code;
    }

    public function getAuxBase(): ?float
    {
        return $this->aux_base;
    }

    public function setAuxBase(float $aux_base): void
    {
        $this->aux_base = $aux_base;
    }

    public function getSensorBase(): ?float
    {
        return $this->sensor_base;
    }

    public function setSensorBase(float $sensor_base): void
    {
        $this->sensor_base = $sensor_base;
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

    public function setStation(Station $station): void
    {
        $this->station = $station;
    }

    public function setVendor(Vendor $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function setDeletedAt(): void
    {
        $this->deleted_at = new DateTime();
    }

    public function restore(): void
    {
        $this->deleted_at = null;
    }

    public function setType(SensorType $type): void
    {
        $this->type = $type;
    }

    public function addData(StationSensorData $data): StationSensorData
    {
        $this->data->add($data);
        $data->setSensor($this);
        return $data;
    }

    public function getInstalledAt(): ?DateTimeInterface
    {
        return $this->installed_at;
    }

    public function setInstalledAt(DateTimeInterface $installed_at): void
    {
        $this->installed_at = $installed_at;
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    public function getAQIData(): Collection
    {
        return $this->aqi_data;
    }

    public function addAQIData(StationSensorAQIData $data): StationSensorAQIData
    {
        $this->aqi_data->add($data);
        $data->setSensor($this);
        return $data;
    }
}
