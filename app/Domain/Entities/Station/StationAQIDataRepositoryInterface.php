<?php

namespace Persium\Station\Domain\Entities\Station;

interface StationAQIDataRepositoryInterface
{
    public function save(StationAQIData $station_aqi_data): void;
    public function flush(): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?StationAQIData;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?StationAQIData;
    public function count(array $criteria): int;
}
