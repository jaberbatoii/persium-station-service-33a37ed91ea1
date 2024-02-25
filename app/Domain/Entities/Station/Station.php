<?php

declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\Station;

use Doctrine\Common\Collections\ArrayCollection;
use Persium\Station\Domain\ValueObjects\StationVO;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Support\Str;

class Station
{
    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 2;
    const STATUS_STALE = 3;

    private int $id;
    private string $uuid;
    private DateTimeInterface $created_at;
    private DateTimeInterface $updated_at;
    private ?DateTimeInterface $deleted_at = null;

    private Collection $sensors;
    private Collection $aqi_data;

    public function __construct(
        private string                     $name,
        private string                     $address,
        private string                     $source,
        private string                     $source_id,
        private float                      $latitude,
        private float                      $longitude,
        private float                      $altitude,
        private ?string                    $photo_url,
        private readonly DateTimeInterface $installed_at,
        private int                        $status = self::STATUS_OFFLINE,
    ) {
        $this->sensors = new ArrayCollection();
        $this->aqi_data = new ArrayCollection();
        $this->uuid = Str::uuid()->toString();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = $this->created_at;
    }

    public static function createFromVO(StationVO $station_vo): self
    {
        return new self(
            name: $station_vo->getName(),
            address: $station_vo->getAddress(),
            source: $station_vo->getSource(),
            source_id: $station_vo->getSourceID(),
            latitude: $station_vo->getLatitude(),
            longitude: $station_vo->getLongitude(),
            altitude: $station_vo->getAltitude(),
            photo_url: $station_vo->getPhotoURL(),
            installed_at: new DateTimeImmutable(),
        );
    }

    public function getInstalledAt(): DateTimeInterface
    {
        return $this->installed_at;
    }

    public function getID(): int
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

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getPhotoURL(): ?string
    {
        return $this->photo_url;
    }

    public function setPhotoURL(?string $photo_url): void
    {
        $this->photo_url = $photo_url;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(): void
    {
        $this->created_at = new DateTime();
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(): void
    {
        $this->deleted_at = new DateTime();
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTime();
    }

    public function setAddress(string $string)
    {
        $this->address = $string;
    }

    public function setSource(string $string)
    {
        $this->source = $string;
    }

    public function setSourceId(string $string)
    {
        $this->source_id = $string;
    }

    public function setLatitude(float $float)
    {
        $this->latitude = $float;
    }

    public function setLongitude(float $float)
    {
        $this->longitude = $float;
    }

    public function setAltitude(float $float)
    {
        $this->altitude = $float;
    }

    public function addSensor(StationSensor $sensor): void
    {
        if ($this->sensors->exists(function ($key, $element) use($sensor) {
            return $element->getName() === $sensor->getName();
        })){
            return;
        }
        $this->sensors->add($sensor);
        $sensor->setStation($this);
    }

    public function getSensors(): ?Collection
    {
        return $this->sensors;
    }

    public function addAQIData(StationAQIData $one_aqi_data): void
    {
        $this->aqi_data->add($one_aqi_data);
        $one_aqi_data->setStation($this);
    }

    public function isOnline(): bool
    {
        return $this->status === self::STATUS_ONLINE;
    }

    public function getCrawlerSource(): string
    {
        $source_configs = config('crawler');
        foreach ($source_configs as $crawler_source => $source_config) {
            if(in_array($this->source, $source_config['sources'])) {
                return $crawler_source;
            }
        }
        return $this->source;
    }

    public function getAQIData(): ?Collection
    {
        return $this->aqi_data;
    }

    public function addSensorData(StationSensor $sensor, StationSensorData $sensor_data): void
    {
        $sensor->addData($sensor_data);
    }

    public function addSensorAQIData(StationSensor $sensor, StationSensorAQIData $sensor_aqi_data): void
    {
        $sensor->addAQIData($sensor_aqi_data);
    }


}
