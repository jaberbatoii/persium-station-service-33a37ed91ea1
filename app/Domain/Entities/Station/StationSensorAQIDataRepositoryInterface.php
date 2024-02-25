<?php

namespace Persium\Station\Domain\Entities\Station;

interface StationSensorAQIDataRepositoryInterface
{
    public function save(StationSensorAQIData $station_sensor_aqi_data): void;
    public function flush(): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?StationSensorAQIData;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?StationSensorAQIData;
    public function count(array $criteria): int;
}
