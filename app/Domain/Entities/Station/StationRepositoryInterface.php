<?php

namespace Persium\Station\Domain\Entities\Station;

use Carbon\Carbon;
use Persium\Station\Domain\Entities\SensorType\SensorType;
use Persium\Station\Domain\ValueObjects\StationDataVO;

interface StationRepositoryInterface
{
    public function save(Station $station): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?Station;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneByUuid(string $uuid): ?Station;
    public function findOneBy(array $criteria, ?array $order_by = null): ?Station;
    public function count(array $criteria): int;

    public function findOneSensorByType(Station $station, SensorType $sensor_type): ?StationSensor;
    public function findNearestOneByLatAndLngInDistanceBySource(float $lat, float $lng, float $distance, array $sources): ?Station;
    public function getLatestDataTime(int $station_id): ?Carbon;
    public function findInBoundWithStatus(
        float $lax_max, float $lat_min,
        float $lng_max, float $lng_min,
        array $statusIds
    ): array;
    public function getLatestAQIDataByAQIType(int $station_id, int $aqi_type) :?StationAQIData;
    public function getLatestDataByStationId(int $station_id): array;
}
