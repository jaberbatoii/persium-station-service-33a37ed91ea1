<?php
declare(strict_types = 1);

namespace Persium\Station\Domain\Entities\SensorType;

interface CrossSensitivityRepositoryInterface
{
    public function save(CrossSensitivity $cross_sensitivity): void;
    public function find(int $id, int $lock_mode = null, ?int $lock_version = null): ?CrossSensitivity;
    public function findBy(array $criteria, ?array $order_by = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria, ?array $order_by = null): ?CrossSensitivity;
    public function count(array $criteria): int;
}
