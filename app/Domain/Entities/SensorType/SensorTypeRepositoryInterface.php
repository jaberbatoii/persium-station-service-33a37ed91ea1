<?php

namespace Persium\Station\Domain\Entities\SensorType;

interface SensorTypeRepositoryInterface
{
    public function save(SensorType $sensor_type): void;
    public function findOneByName(string $name): ?SensorType;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?SensorType;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?SensorType;
    public function count(array $criteria): int;
}
