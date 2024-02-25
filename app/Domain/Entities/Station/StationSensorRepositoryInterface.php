<?php

namespace Persium\Station\Domain\Entities\Station;

interface StationSensorRepositoryInterface
{
    public function save(StationSensor $station_sensor): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?StationSensor;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?StationSensor;
    public function count(array $criteria): int;
    public function findOneByStationAndSensorType(int $station_id, int $sensor_type_id): ?StationSensor;
}
