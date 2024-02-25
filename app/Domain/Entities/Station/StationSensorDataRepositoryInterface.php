<?php

namespace Persium\Station\Domain\Entities\Station;

interface StationSensorDataRepositoryInterface
{
    public function save(StationSensorData $station_sensor_data): void;
    public function flush(): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?StationSensorData;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?StationSensorData;
    public function count(array $criteria): int;
}
